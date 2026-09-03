<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\ClientType;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            // Add new client_type column (string) with default value for existing rows
                        // Add new client_type column (nullable string) after entity_type
            $table->string('client_type')->nullable()->after('entity_type');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
                        // Remove client_type column
            $table->dropColumn('client_type');
        });
    }
};
?>
