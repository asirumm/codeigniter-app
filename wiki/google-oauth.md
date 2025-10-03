# Google Oauth Service

Pada project ini user dapat login dengan google gmail (tujuan utama),
selanjutnya untuk konfigurasi:

1. Buat akun google cloud console project
2. Buat project
3. Buat API Oauth Consent Screen
4. Simpan API Key, client id dan simpan url callback (atau isikan 'api/login/callback')
5. Apabila kalian berbeda url callbacknya konfigurasikan pada routes di aplikasi project
`app\ApplicationRoutes\guest`

Note : jika belum paham silahkan lihat di youtube atau web


Konfigurasi Aplikasi Project
1. Pada env buat variable dan sesuaikan isinya
```
google.oauth.client.id
google.oauth.client.secret
```
2. Jalankan command `php spark key:generate` untuk generate key jwt, atau kalian
bisa untuk membuatnya manual dengan key `encryption.key` valuenya seterah kalian.
3. Pergi ke `app\Security\InMemoryUserProvider` taruh nama email kalian pada username,
untuk tes login.
4. Selanjutnya coba login pada url `login`, gunakan gmail yang sesuai dengan yang anda isikan
 sebelumnya pada langkah ke 3. Nantinya jika berhasil ada cookie api key bernama `AUTH-COOKIE`
5. Jika error bisa kalian cek di logging pada writeable.


### Flow alur
1. User akan diberikan login link pada form login (lihat di view/login pada bagian link)
2. Callback akan terjadi di `api/login/callback` dan akan ditangani oleh filter OauthGoogleAuthentication
yang mana filter tersebut akan mengembalikan jwt cookie dengan expired yang sudah diatur pada konfig jwt
yang pasti lebih lama dari google oauth response.
3. Filter tersebut akan berkoordinasi dengan security default provider InMemoryUserProvider, apabila
gmail ditemukan maka dikembalikan jwt yang sesuai dengan data tersebut.
4. Selanjutnya untuk halaman lain tinggal diberikan filter authorization untuk validasi cookie dan hak akses.

