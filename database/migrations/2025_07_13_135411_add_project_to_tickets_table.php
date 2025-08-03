    <?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration
    {
        /**
         * Jalankan migrasi.
         */
        public function up(): void
        {
            Schema::table('tickets', function (Blueprint $table) {
                // Tambahkan kolom 'project' setelah kolom 'requester_id'
                $table->string('project', 255)->nullable()->after('requester_id');
            });
        }

        /**
         * Mengembalikan migrasi.
         */
        public function down(): void
        {
            Schema::table('tickets', function (Blueprint $table) {
                // Hapus kolom 'project' jika migrasi di-rollback
                $table->dropColumn('project');
            });
        }
    };
    