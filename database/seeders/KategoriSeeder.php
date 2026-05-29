<?php

namespace Database\Seeders;

use App\Models\Kategoriperusahaan;
use App\Models\Kategorilowongan;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategoriPerusahaan = [
            'Manufaktur', 'Perbankan & Keuangan', 'Teknologi Informasi', 'Kesehatan & Medis',
            'Pendidikan', 'Konstruksi & Properti', 'Transportasi & Logistik', 'Perhotelan & Pariwisata',
            'Ritel & Perdagangan', 'Energi & Sumber Daya Mineral', 'Pertanian & Kehutanan',
            'Telekomunikasi', 'Media & Hiburan', 'Otomotif', 'Tekstil & Garmen',
            'Makanan & Minuman', 'Farmasi & Kosmetik', 'Hukum & Konsultasi', 'Kelautan & Perikanan',
            'Pertahanan & Keamanan', 'Fashion & Gaya Hidup', 'Layanan Publik & Pemerintah',
            'Asuransi', 'Agensi Kreatif & Periklanan', 'Startup & Teknologi Baru', 'Elektronik',
            'Kimia & Plastik', 'Pertambangan', 'Penerbangan & Kedirgantaraan', 'Event & MICE',
            'Pengelolaan Limbah & Lingkungan', 'Logistik & Warehousing', 'E-commerce', 
            'Agribisnis', 'Ekspor Impor', 'Kerajinan & Seni Budaya', 'Layanan Kebersihan',
            'Olahraga & Kebugaran', 'Layanan Profesional Lainnya'
        ];

        foreach ($kategoriPerusahaan as $nama) {
            Kategoriperusahaan::firstOrCreate(
                ['nama' => $nama],
                ['useradd' => 1]
            );
        }

        $kategoriLowongan = [
            // IT & Software
            'Software Engineer', 'Backend Developer', 'Frontend Developer', 'Fullstack Developer',
            'Mobile Developer', 'UI/UX Designer', 'QA Tester / Quality Assurance', 'DevOps Engineer',
            'Data Scientist / Data Analyst', 'AI / Machine Learning Engineer', 'Cybersecurity Specialist',
            'Network & System Administrator', 'Database Administrator', 'Technical Support',
            
            // Business & Finance
            'Accounting', 'Finance / Treasury', 'Tax Specialist', 'Auditor',
            'Business Development', 'Marketing Executive', 'Sales / Account Executive',
            'Digital Marketing Specialist', 'SEO / SEM Specialist', 'Social Media Manager',
            'Public Relations / PR', 'Brand Manager', 'Market Researcher',
            
            // Creative & Content
            'Graphic Designer', 'Video Editor / Videographer', 'Motion Designer / Animator',
            'Content Writer / Copywriter', 'Photographer', 'Creative Director', 'Art Director',
            'Translator / Interpreter',
            
            // Engineering & Construction
            'Civil Engineer', 'Mechanical Engineer', 'Electrical Engineer', 'Industrial Engineer',
            'Architect', 'Interior Designer', 'Project Manager (Construction)', 'Draftsman',
            'Quantity Surveyor (QS)',
            
            // Healthcare
            'Dokter (Umum/Spesialis)', 'Perawat', 'Apoteker', 'Bidan',
            'Analis Kesehatan / Laboratorium', 'Fisioterapis', 'Nutritionist',
            
            // Education
            'Guru / Pengajar', 'Dosen', 'Trainer / Instruktor', 'Tutor',
            'Akademik & Kurikulum',
            
            // Hospitality & Retail
            'Chef / Cook', 'Barista', 'Waiter / Waitress', 'Restaurant Manager',
            'Hotel Front Desk', 'Housekeeping', 'Tour Guide / Travel Consultant',
            'Store Manager', 'Retail Sales Associate', 'Cashier',
            
            // Human Resources & Admin
            'HR Generalist / Manager', 'Recruiter / Talent Acquisition', 'Payroll Specialist',
            'Personal Assistant / Secretary', 'Administrative Staff', 'Office Manager',
            
            // Operational & Others
            'Logistics / Supply Chain Manager', 'Warehouse Operator', 'Inventory Clerk',
            'Driver (SIM A/B/C)', 'Security Guard', 'Courier / Delivery',
            'Production Operator / Assembly', 'Quality Control (QC) Inspector', 'Safety Officer (K3)',
            'Cleaning Service / Janitor / Maintenance', 'Customer Service / Call Center',
            'Legal Counsel / Lawyer', 'Pharmacist Assistant', 'Librarian'
        ];

        foreach ($kategoriLowongan as $nama) {
            Kategorilowongan::firstOrCreate(
                ['nama' => $nama],
                ['useradd' => 1]
            );
        }
    }
}
