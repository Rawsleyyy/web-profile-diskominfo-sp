# Website Profil Instansi Dinamis Berbasis CMS

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-Backend-red?style=for-the-badge&logo=laravel">
  <img src="https://img.shields.io/badge/React-Frontend-blue?style=for-the-badge&logo=react">
  <img src="https://img.shields.io/badge/TailwindCSS-UI-06B6D4?style=for-the-badge&logo=tailwindcss">
</p>

## Tentang Project

Website Profil Instansi Dinamis merupakan sistem informasi berbasis web yang dikembangkan untuk membantu instansi dalam menyampaikan informasi publik serta mengelola konten website secara lebih fleksibel.

Project ini menerapkan konsep **Content Management System (CMS)** sehingga administrator dapat mengelola berbagai informasi melalui dashboard tanpa perlu melakukan perubahan langsung pada source code.

Website ini dikembangkan menggunakan:

- **Laravel** sebagai backend API dan pengelolaan data
- **React** sebagai frontend interface
- **MySQL/PostgreSQL** sebagai database
- **REST API** sebagai komunikasi antara backend dan frontend


## Latar Belakang

Website profil instansi sebelumnya memiliki beberapa keterbatasan, seperti:

- Tampilan belum sepenuhnya responsif pada berbagai perangkat.
- Beberapa elemen tampilan belum konsisten.
- Pengaturan konten halaman utama masih terbatas.
- Perubahan tampilan tertentu masih membutuhkan perubahan source code.
- Pengelolaan konten belum sepenuhnya fleksibel.

Oleh karena itu, dikembangkan website baru dengan konsep CMS yang memungkinkan administrator mengelola konten secara lebih mudah dan dinamis.


# Fitur Utama

## Public Website

### Informasi Instansi
- Profil instansi
- Visi dan misi
- Tugas pokok dan fungsi
- Struktur organisasi berbasis tree

### Publikasi Informasi
- Berita
- Artikel
- Podcast
- Pengumuman
- Penghargaan

### Layanan Publik
- PPID
- Data publik
- Standar pelayanan
- Maklumat pelayanan
- Survey Kepuasan Masyarakat


## CMS Dashboard Administrator

Administrator dapat mengelola:

### Website Configuration
- Logo instansi
- Informasi kontak
- Footer
- Google Maps
- Theme preset

### Homepage Builder
- Mengatur section halaman utama
- Mengatur urutan tampilan section
- Mengaktifkan/nonaktifkan section tertentu

### Navigation Management
- Menambah menu navbar
- Mengubah menu
- Menghapus menu
- Dropdown menu
- Internal route
- External link
- Module based menu


### Content Management

- Banner
- Berita
- Artikel
- Podcast
- FAQ
- Penghargaan
- Struktur organisasi
- PPID
- Data publik


# Arsitektur Sistem

Project menggunakan konsep pemisahan frontend dan backend.

```
User
 |
 |
Frontend React
 |
 | REST API
 |
Backend Laravel
 |
 |
Database
```


## Backend

Backend bertanggung jawab terhadap:

- Authentication
- Authorization
- Database management
- Business logic
- API endpoint
- File upload
- Content management


## Frontend

Frontend bertanggung jawab terhadap:

- Tampilan website
- Responsive layout
- User interaction
- Pengambilan data melalui API
- Rendering konten dinamis


# Teknologi yang Digunakan

## Backend

| Teknologi | Fungsi |
|---|---|
| Laravel | Framework backend |
| Laravel Sanctum | Authentication API |
| MySQL/PostgreSQL | Database |
| Eloquent ORM | Database interaction |
| REST API | Komunikasi data |


## Frontend

| Teknologi | Fungsi |
|---|---|
| React JS | User Interface |
| React Router | Routing |
| Tailwind CSS | Styling |
| Axios | HTTP Request |
| Lucide Icon | Icon library |


# Instalasi Project


## 1. Clone Repository

```bash
git clone https://github.com/username/project-name.git
```

Masuk ke folder:

```bash
cd project-name
```


# Setup Backend Laravel


Masuk folder backend:

```bash
cd backend
```


Install dependency:

```bash
composer install
```


Copy file environment:

```bash
cp .env.example .env
```


Generate application key:

```bash
php artisan key:generate
```


Konfigurasi database pada:

```
.env
```


Contoh:

```env
DB_DATABASE=nama_database
DB_USERNAME=root
DB_PASSWORD=
```


Jalankan migration:

```bash
php artisan migrate
```


Jika terdapat data awal:

```bash
php artisan db:seed
```


Jalankan server Laravel:

```bash
php artisan serve
```


Backend berjalan pada:

```
http://127.0.0.1:8000
```



# Setup Frontend React


Masuk folder frontend:

```bash
cd frontend
```


Install dependency:

```bash
npm install
```


Buat file:

```
.env
```


Isi:

```env
VITE_API_URL=http://127.0.0.1:8000/api
```


Jalankan frontend:

```bash
npm run dev
```


Frontend berjalan pada:

```
http://localhost:5173
```


# Struktur Folder


```
backend
|
├── app
│   ├── Models
│   ├── Http
│   │   ├── Controllers
│   │   └── Requests
│
├── database
│   ├── migrations
│   └── seeders
│
└── routes
    └── api.php



frontend
|
├── src
│
├── components
│
├── pages
│
├── services
│
├── hooks
│
└── context
```


# Konsep Dynamic CMS

Website menggunakan konsep data-driven.

Contoh:

Navbar tidak dibuat secara hardcode.

Data menu disimpan pada database:

```
Menu
 |
 |-- Label
 |-- URL
 |-- Type
 |-- Parent ID
 |-- Status
```

Kemudian frontend membaca data tersebut melalui API.


Hal ini memungkinkan:

- Menu dapat berubah tanpa mengubah kode.
- Website dapat digunakan untuk kebutuhan instansi berbeda.
- Administrator dapat mengatur tampilan website melalui dashboard.


# API Documentation

Contoh endpoint:


## Authentication

```
POST /api/login
```

## Website Configuration

```
GET /api/settings
```

## Navigation

```
GET /api/navigation
```


## News

```
GET /api/news
```


## Awards

```
GET /api/awards
```


## Organization Structure

```
GET /api/pejabat
```


# Development


Menjalankan backend:

```bash
php artisan serve
```


Menjalankan frontend:

```bash
npm run dev
```


Build production:

Frontend:

```bash
npm run build
```


Laravel:

```bash
php artisan optimize
```


# Future Development

Beberapa pengembangan yang dapat dilakukan:

- SEO Management
- Advanced Role Permission
- Theme Builder
- Multi-instansi support
- Analytics dashboard
- Progressive Web App (PWA)


# Author

Developed by:

**Narendra Lintang Saputra**

Praktik Industri  
Dinas Komunikasi, Informatika, Statistik, dan Persandian Kota Surakarta


# License

Project ini dibuat untuk kebutuhan pengembangan website profil instansi.
