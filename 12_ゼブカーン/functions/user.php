<?php

// Save user to the users.csv file
function saveUser(array $user): array
{
    $handle = fopen(__DIR__ . '/../data/users.csv', 'a');

    // Assign a new unique ID to the user
    $user['id'] = getNewId();

    // Write user details to CSV
    fputcsv($handle, [
        $user['id'],
        $user['name'],
        $user['email'],
        password_hash($user['password'], PASSWORD_DEFAULT),
        $user['dob'],
        $user['phone'],
        $user['gender'],
        $user['address']
    ]);

    fclose($handle);

    return $user;
}

function getUsers(): array
{
    $handle = fopen(__DIR__ . '/../data/users.csv', 'r');
    $users = [];

    // Loop through CSV and construct users array
    while (($row = fgetcsv($handle)) !== false) {
        if (!empty($row[0])) {
            // Ensure we avoid accessing non-existent fields by checking if the field exists before accessing it
            $users[] = [
                'id' => $row[0],
                'name' => $row[1],
                'email' => $row[2],
                'password' => $row[3],
                'dob' => $row[4],
                'phone' => $row[5],
                'gender' => $row[6] ?? '',
                'address' => $row[7] ?? ''
            ];
        }
    }

    fclose($handle);

    return $users;
}

// Generate a new unique ID for the next user
function getNewId(): int
{
    $users = getUsers();
    $maxId = 0;

    // Find the maximum ID in the users list
    foreach ($users as $user) {
        if ($user['id'] > $maxId) {
            $maxId = $user['id'];
        }
    }

    return $maxId + 1;
}

// Handle user login
function login(string $email, string $password): ?array
{
    $users = getUsers();

    // Check if the email exists and password matches
    foreach ($users as $user) {
        if ($user['email'] === $email && password_verify($password, $user['password'])) {
            return $user;
        }
    }

    return null;
}

// Retrieve user by ID
function getUser(int|string $id): ?array
{
    $users = getUsers();

    // Find and return the user with the given ID
    foreach ($users as $user) {
        if (intval($user['id']) === intval($id)) {
            return $user;
        }
    }

    return null;
}




function updateUser(int $id, array $updatedData)
{
    $users = getUsers();
    $handle = fopen(__DIR__ . '/../data/users.csv', 'w');

    foreach ($users as $user) {
        if ($user['id'] == $id) {
            $user = array_merge($user, $updatedData);
        }
        fputcsv($handle, [
            $user['id'],
            $user['name'],
            $user['email'],
            $user['password'],
            $user['dob'],
            $user['phone'],
            $user['gender'],
            $user['address']
        ]);
    }

    fclose($handle);
}

