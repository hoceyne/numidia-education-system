<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Mail\VerifyEmail;
use App\Models\Admin;
use App\Models\Branch;
use App\Models\File;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Branch::create([
            'name' => "numidia school",
        ]);
        Branch::create([
            'name' => "numidia profession",
        ]);
        Branch::create([
            'name' => "numidia academy",
        ]);

        $content = Storage::get('default-profile-picture.jpeg');
        $extension = 'jpeg';
        $name = "profile picture";
        $code =  Str::random(6);
        $user = User::create([
            'name' => "Numidia Admin",
            'email' =>env('APP_MAIL_ADMIN'),
            'role' => "role",
            'gender' => "Male",
            'password' => Hash::make($code ),
            'code' => $code,
        ]);
        $user->admin()->save(new Admin());

        $user->profile_picture()->save(new File([
            'name' => $name,
            'content' => base64_encode($content),
            'extension' => $extension,
        ]));

        try {
            //code...
            $data = [
                'url' => env('APP_URL') . '/api/email/verify?id=' . $user->id . '&code=' . $user->code,
                'name' => $user->name,
                'email' => $user->email,
                'code' => $user->code,
            ];
            Mail::to($user)->send(new VerifyEmail($data));
        } catch (\Throwable $th) {
            //throw $th;
            abort(400);
        }
    }
}
