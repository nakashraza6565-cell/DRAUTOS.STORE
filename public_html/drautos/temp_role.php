$u = \App\User::where('email','admin@gmail.com')->first();
if($u && !$u->hasRole('admin')) {
    $u->assignRole('admin');
    echo "Assigned admin role to admin@gmail.com\n";
} else {
    echo "Already has role or not found\n";
}
