<?php

namespace App\Enums;

enum EmployeeType: string
{
    case Dokter = 'dokter';
    case Perawat = 'perawat';
    case Apoteker = 'apoteker';
    case Kasir = 'kasir';
    case Analis = 'analis';
    case Radiografer = 'radiografer';
    case Resepsionis = 'resepsionis';
}
