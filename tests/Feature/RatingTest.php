<?php

namespace Tests\Feature;

use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\Ulasan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RatingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function login(string $nis, string $pin = '1234'): string
    {
        Anggota::firstOrCreate(['nis' => $nis], [
            'nama' => 'Siswa '.$nis,
            'kelas' => 'X',
            'pin' => Hash::make($pin),
        ]);
        $resp = $this->postJson('/api/v1/login', ['nis' => $nis, 'pin' => $pin])->assertOk();

        return $resp->json('data.token');
    }

    private function tokenForReturnedLoan(): array
    {
        // From PeminjamanSeeder: returned loan nis 20240103, buku 'Bumi Manusia'
        $token = $this->login('20240103');
        $peminjaman = Peminjaman::where('nis', '20240103')
            ->where('status', Peminjaman::STATUS_DIKEMBALIKAN)
            ->firstOrFail();

        return [$token, $peminjaman->id];
    }

    public function test_rating_requires_authentication(): void
    {
        [$token, $peminjamanId] = $this->tokenForReturnedLoan();
        $this->postJson('/api/v1/peminjaman/'.$peminjamanId.'/rating', ['rating' => 5])
            ->assertUnauthorized();
    }

    public function test_rating_forbidden_for_non_owner(): void
    {
        $ownerToken = $this->login('20240103');
        $otherToken = $this->login('99999999');
        $peminjaman = Peminjaman::where('nis', '20240103')
            ->where('status', Peminjaman::STATUS_DIKEMBALIKAN)
            ->firstOrFail();

        $this->withHeader('Authorization', 'Bearer '.$otherToken)
            ->postJson('/api/v1/peminjaman/'.$peminjaman->id.'/rating', ['rating' => 4])
            ->assertForbidden();
    }

    public function test_rating_rejected_before_return(): void
    {
        $token = $this->login('20240101'); // dipinjam per seeder
        $peminjaman = Peminjaman::where('nis', '20240101')
            ->where('status', Peminjaman::STATUS_DIPINJAM)
            ->firstOrFail();

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/peminjaman/'.$peminjaman->id.'/rating', ['rating' => 3])
            ->assertStatus(422)
            ->assertJsonPath('message', 'Rating hanya dapat diberikan setelah buku dikembalikan.');
    }

    public function test_rating_valid_creates_ulasan(): void
    {
        [$token, $peminjamanId] = $this->tokenForReturnedLoan();

        $resp = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/peminjaman/'.$peminjamanId.'/rating', [
                'rating' => 5,
                'komentar' => 'Buku bagus sekali!',
            ])
            ->assertCreated();

        $resp->assertJsonPath('data.rating', 5)
            ->assertJsonPath('data.komentar', 'Buku bagus sekali!')
            ->assertJsonPath('data.peminjaman_id', $peminjamanId);

        $this->assertDatabaseHas('ulasan', [
            'peminjaman_id' => $peminjamanId,
            'buku_id' => $resp->json('data.buku_id'),
            'nis' => '20240103',
            'rating' => 5,
            'komentar' => 'Buku bagus sekali!',
        ]);
    }

    public function test_rating_can_be_updated(): void
    {
        [$token, $peminjamanId] = $this->tokenForReturnedLoan();

        // First rating
        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/peminjaman/'.$peminjamanId.'/rating', ['rating' => 3, 'komentar' => 'Biasa saja'])
            ->assertCreated();

        // Update rating
        $resp = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/peminjaman/'.$peminjamanId.'/rating', ['rating' => 4, 'komentar' => 'Lebih baik dari yang dikira'])
            ->assertOk();

        $resp->assertJsonPath('data.rating', 4)
            ->assertJsonPath('data.komentar', 'Lebih baik dari yang dikira');

        $this->assertDatabaseHas('ulasan', [
            'peminjaman_id' => $peminjamanId,
            'rating' => 4,
            'komentar' => 'Lebih baik dari yang dikira',
        ]);

        // Ensure only one row
        $this->assertEquals(1, Ulasan::where('peminjaman_id', $peminjamanId)->count());
    }

    public function test_rating_validation_min_max(): void
    {
        [$token, $peminjamanId] = $this->tokenForReturnedLoan();

        foreach ([0, 6, -1, 10] as $invalid) {
            $this->withHeader('Authorization', 'Bearer '.$token)
                ->postJson('/api/v1/peminjaman/'.$peminjamanId.'/rating', ['rating' => $invalid])
                ->assertStatus(422);
        }
    }

    public function test_rating_komentar_max_length(): void
    {
        [$token, $peminjamanId] = $this->tokenForReturnedLoan();

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/peminjaman/'.$peminjamanId.'/rating', [
                'rating' => 5,
                'komentar' => str_repeat('a', 1001),
            ])
            ->assertStatus(422);
    }

    public function test_rating_komentar_optional(): void
    {
        [$token, $peminjamanId] = $this->tokenForReturnedLoan();

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/peminjaman/'.$peminjamanId.'/rating', ['rating' => 4])
            ->assertCreated()
            ->assertJsonPath('data.komentar', null);
    }

    public function test_buku_resource_exposes_average_rating_and_count(): void
    {
        [$token, $peminjamanId] = $this->tokenForReturnedLoan();

        // Add two ratings on same book via two different returned loans
        // Create second returned loan manually
        $anggota2 = Anggota::create(['nis' => '20240199', 'nama' => 'Siswa 2', 'kelas' => 'X', 'pin' => Hash::make('1234')]);
        $token2 = $this->login('20240199');
        $buku = Buku::where('judul', 'Bumi Manusia')->firstOrFail();
        $buku->decrement('stok_tersedia');
        $p2 = Peminjaman::create([
            'nama_peminjam' => 'Siswa 2',
            'nis' => '20240199',
            'buku_id' => $buku->id,
            'tanggal_pinjam' => now()->subDays(5)->toDateString(),
            'tanggal_jatuh_tempo' => now()->subDays(1)->toDateString(),
            'status' => Peminjaman::STATUS_DIPINJAM,
        ]);
        Pengembalian::create(['peminjaman_id' => $p2->id, 'tanggal_kembali' => now()->subDays(1)->toDateString()]);
        $p2->update(['status' => Peminjaman::STATUS_DIKEMBALIKAN]);
        $buku->increment('stok_tersedia');

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/peminjaman/'.$peminjamanId.'/rating', ['rating' => 5])
            ->assertCreated();

        $this->withHeader('Authorization', 'Bearer '.$token2)
            ->postJson('/api/v1/peminjaman/'.$p2->id.'/rating', ['rating' => 3])
            ->assertCreated();

        // Fetch book list, expect rating avg = 4.0
        $resp = $this->getJson('/api/v1/buku?search=Bumi Manusia')->assertOk();
        $data = $resp->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals(4.0, $data[0]['rating']);
        $this->assertEquals(2, $data[0]['ulasan_count']);
    }

    public function test_peminjaman_resource_includes_ulasan_and_bisa_rating(): void
    {
        [$token, $peminjamanId] = $this->tokenForReturnedLoan();

        // No rating yet
        $resp = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/peminjaman/'.$peminjamanId)
            ->assertOk();
        $data = $resp->json('data');
        $this->assertNull($data['ulasan']);
        $this->assertTrue($data['bisa_rating']);

        // After rating
        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/peminjaman/'.$peminjamanId.'/rating', ['rating' => 4])
            ->assertCreated();

        $resp = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/peminjaman/'.$peminjamanId)
            ->assertOk();
        $data = $resp->json('data');
        $this->assertEquals(4, $data['ulasan']['rating']);
        $this->assertTrue($data['bisa_rating']);
    }
}
