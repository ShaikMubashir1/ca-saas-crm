<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ClientPortalToken extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'client_id',
        'document_request_id',
        'token_hash',
        'expires_at',
        'last_used_at',
        'revoked_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'last_used_at' => 'datetime',
            'revoked_at' => 'datetime',
        ];
    }

    public static function generate(Client $client, ?DocumentRequest $docReq = null, int $daysValid = 14): array
    {
        $plainToken = Str::random(40);
        $tokenHash = hash('sha256', $plainToken);

        $record = static::create([
            'tenant_id' => $client->tenant_id,
            'client_id' => $client->id,
            'document_request_id' => $docReq?->id,
            'token_hash' => $tokenHash,
            'expires_at' => now()->addDays($daysValid),
        ]);

        return [
            'plain_token' => $plainToken,
            'record' => $record,
        ];
    }

    public static function findValidToken(string $plainToken): ?static
    {
        $hash = hash('sha256', $plainToken);

        $token = static::where('token_hash', $hash)
            ->whereNull('revoked_at')
            ->where('expires_at', '>', now())
            ->first();

        if ($token) {
            $token->update(['last_used_at' => now()]);
        }

        return $token;
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isRevoked(): bool
    {
        return !is_null($this->revoked_at);
    }

    public function revoke(): void
    {
        $this->update(['revoked_at' => now()]);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function documentRequest(): BelongsTo
    {
        return $this->belongsTo(DocumentRequest::class);
    }
}
