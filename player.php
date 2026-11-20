
<a href="/">menu</a>

<?php

$clientId = '';
$clientSecret = '';


$protocole = 'https://';
$sousDomaine = 'osu.';
$domaine = 'ppy.sh';
$baseUrl = $protocole . $sousDomaine . $domaine;

function getOsuToken($clientId, $clientSecret, $baseUrl) {
    $url = $baseUrl . '/oauth/token';
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'client_id' => $clientId,
        'client_secret' => $clientSecret,
        'grant_type' => 'client_credentials',
        'scope' => 'public'
    ]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
        'Content-Type: application/x-www-form-urlencoded',
        'User-Agent: OsuPhpApp/1.0'
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    //curl_close($ch);

    $data = json_decode($response, true);
    return $data['access_token'] ?? null;
}

function fetchOsuApi($endpoint, $token, $baseUrl) {
    $url = $baseUrl . '/api/v2/users/' . ltrim($endpoint, '/');
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
        'Content-Type: application/json',
        'Authorization: Bearer ' . $token,
        'User-Agent: OsuPhpApp/1.0'
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
   // curl_close($ch);

    return json_decode($response, true);
}

$token = getOsuToken($clientId, $clientSecret, $baseUrl);
    $nom_compte = 'mrekk';
    if (isset($_POST['valider'])) {
        $nom_compte = $_POST['zone_texte'];
}


    echo '
        <form method="POST" action="">
            <input type="text" name="zone_texte" placeholder="Écrivez ici...">
            <button type="submit" name="valider">Envoyer</button>
        </form>
        ';
if ($token) {
    $username = '@' . $nom_compte;
    $userData = fetchOsuApi($username, $token, $baseUrl);

    if (isset($userData['username'])) {
        
        $avatarUrl = htmlspecialchars($userData['avatar_url']);
        $bannerUrl = htmlspecialchars($userData['cover']['url']);
        $pseudo = htmlspecialchars($userData['username']);


        echo "<img src='{$bannerUrl}' alt='banner de {$pseudo}' '><br><br>";
        echo "<img src='{$avatarUrl}' alt='Avatar de {$pseudo}' width='120' height='120' style='border-radius: 12px;'><br><br>";
        echo "Joueur : " . $pseudo . "<br>";
        echo "PP : " . ($userData['statistics']['pp'] ?? 0) . "<br>";

        echo "pays : " . ($userData['country_code'] ?? "") . "<br>";
        echo "Rang mondial : #" . ($userData['statistics']['global_rank'] ?? 'Non classé') . "<br>";
        echo "date d'inscription : " . ($userData['join_date'] ?? "") . "<br>";
        echo "discord : " . ($userData['discord'] ?? "") . "<br>";
        echo "moi : " . ($userData['page']['html']);
    } else {
        echo "Erreur API : ";
        var_dump($userData);
    }
}
