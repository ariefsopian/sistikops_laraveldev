<h1 id="sistikops-laravel-application">SISTIKOPS Laravel Application</h1>
<p>Sebuah aplikasi web berbasis Laravel untuk sistem manajemen tiket operasional (SISTIKOPS). Aplikasi ini memungkinkan pengguna untuk mengelola tiket, menambahkan lampiran, dan melacak riwayat perubahan pada setiap tiket.</p>
<h2 id="fitur-utama">Fitur Utama</h2>
<ul>
<li><strong>Manajemen Tiket:</strong> Membuat, melihat, memperbarui, dan menghapus tiket.</li>
<li><strong>Dukungan Lampiran:</strong> Menambahkan file lampiran ke setiap tiket.</li>
<li><strong>Manajemen Pengguna:</strong> Mengelola pengguna dengan peran yang berbeda.</li>
<li><strong>Logging Aktivitas:</strong> Melacak setiap perubahan yang terjadi pada tiket.</li>
<li><strong>Sistem Otentikasi:</strong> Fitur login untuk pengguna.</li>
</ul>
<h2 id="teknologi-yang-digunakan">Teknologi yang Digunakan</h2>
<ul>
<li><strong>Framework:</strong> Laravel</li>
<li><strong>Bahasa Pemrograman:</strong> PHP, JavaScript, CSS</li>
<li><strong>Database:</strong> MySQL</li>
<li><strong>Asset Bundling:</strong> Vite</li>
<li><strong>Package Manager:</strong> Composer (PHP), npm (Node.js)</li>
</ul>
<h2 id="prasyarat">Prasyarat</h2>
<p>Sebelum memulai, pastikan lingkungan server Anda memiliki komponen berikut terinstal:</p>
<ul>
<li><strong>Web Server:</strong> NGINX atau Apache</li>
<li><strong>PHP:</strong> Versi 8.0.2 atau lebih tinggi</li>
<li><strong>Database:</strong> MySQL 5.7+</li>
<li><strong>Composer</strong></li>
<li><strong>Node.js dan npm</strong></li>
<li><strong>Git</strong></li>
</ul>
<h2 id="langkah-langkah-deployment">Langkah-langkah Deployment</h2>
<p>Ikuti langkah-langkah berikut untuk melakukan deployment proyek ini ke server Anda.</p>
<h3 id="1-kloning-repositori">1. Kloning Repositori</h3>
<p>Masuk ke server Anda melalui SSH dan kloning proyek dari GitHub:</p>
<pre><code class="language-bash">git clone https://github.com/ariefsopian/sistikops_laraveldev.git
cd sistikops_laraveldev
</code></pre>
<h3 id="2-konfigurasi-lingkungan">2. Konfigurasi Lingkungan</h3>
<p>Salin file <code>.env.example</code> dan buat file <code>.env</code> baru.</p>
<pre><code class="language-bash">cp .env.example .env
</code></pre>
<p>Buka file <code>.env</code> dan sesuaikan pengaturan database serta URL aplikasi Anda:</p>
<pre><code class="language-ini">APP_NAME=SISTIKOPS
APP_URL=http://aplikasi-anda.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sistikops_db
DB_USERNAME=sistikops_user
DB_PASSWORD=sandi_rahasia
</code></pre>
<p>Setelah itu, buat <code>APP_KEY</code> baru:</p>
<pre><code class="language-bash">php artisan key:generate
</code></pre>
<h3 id="3-instalasi-dependensi">3. Instalasi Dependensi</h3>
<p>Instal dependensi PHP dan JavaScript yang diperlukan:</p>
<pre><code class="language-bash">composer install --no-dev
npm install
</code></pre>
<p>Kemudian, jalankan build untuk aset front-end menggunakan Vite:</p>
<pre><code class="language-bash">npm run build
</code></pre>
<h3 id="4-penyiapan-database">4. Penyiapan Database</h3>
<p>Jalankan migrasi untuk membuat tabel-tabel di database, lalu jalankan seeder untuk mengisi data awal:</p>
<pre><code class="language-bash">php artisan migrate
php artisan db:seed
</code></pre>
<h3 id="5-konfigurasi-penyimpanan-dan-izin-file">5. Konfigurasi Penyimpanan dan Izin File</h3>
<p>Buat symbolic link untuk storage dan atur izin folder yang diperlukan:</p>
<pre><code class="language-bash">php artisan storage:link
chmod -R 775 storage bootstrap/cache
</code></pre>
<h3 id="6-konfigurasi-web-server">6. Konfigurasi Web Server</h3>
<p>Arahkan <code>Document Root</code> dari web server (NGINX atau Apache) Anda ke folder <code>public</code> dari proyek ini.</p>
<p><strong>Contoh Konfigurasi NGINX:</strong></p>
<pre><code class="language-nginx">server {
    listen 80;
    server_name aplikasi-anda.com;
    root /path/ke/proyek/sistikops_laraveldev/public;

    # ... konfigurasi lainnya ...

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```</pre>
<p>Setelah konfigurasi web server selesai, restart layanan web server Anda. Aplikasi Anda sekarang sudah siap untuk diakses.</p>
