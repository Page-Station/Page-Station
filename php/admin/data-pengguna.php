<?php
session_start();
include('../database.php');

// Ambil semua pengguna
$query_users = "SELECT * FROM users";
$result_users = $conn->query($query_users);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pengguna</title>
    <link rel="stylesheet" href="../../css/kelola-buku&pengguna.css">
    <style>
        .btn-block, .btn-unblock {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-block {
            background-color: #007bff;
            color: white;
        }
        .btn-unblock {
            background-color: green;
            color: white;
        }
    </style>
</head>
<body>
<section class="users-section">
    <h2>Daftar Pengguna</h2>
    <table>
        <tr>
            <th>Username</th>
            <th>IP Address</th>
            <th>Buku yang Dibaca</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
        <?php while ($user = $result_users->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($user['username']) ?></td>
                <td>
                    <?php
                    $ip = $user['ip_address'] ?? '-';
                    echo ($ip === '::1') ? '127.0.0.1' : $ip;
                    ?>
                </td>
                <td>
                    <?php
                    if (isset($user['book_id']) && is_numeric($user['book_id'])) {
                        $stmt = $conn->prepare("SELECT title FROM books WHERE id = ?");
                        $stmt->bind_param('i', $user['book_id']);
                        $stmt->execute();
                        $book_result = $stmt->get_result();
                        if ($book = $book_result->fetch_assoc()) {
                            echo htmlspecialchars($book['title']);
                        } else {
                            echo "Tidak ada buku yang dibaca";
                        }
                    } else {
                        echo "Tidak ada buku yang dibaca";
                    }
                    ?>
                </td>
                <td><?= ($user['is_blocked'] ?? 0) == 1 ? '<span style="color:red">Diblokir</span>' : 'Aktif'; ?></td>
                <td>
                    <?php if (($user['is_blocked'] ?? 0) == 1): ?>
                        <form method="POST" action="unblock-user.php">
                            <input type="hidden" name="user_id" value="<?= $user['id']; ?>">
                            <button type="submit" class="btn-unblock">Buka Blokir</button>
                        </form>
                    <?php else: ?>
                        <form method="POST" action="block-user.php">
                            <input type="hidden" name="user_id" value="<?= $user['id']; ?>">
                            <button type="submit" class="btn-block">Blokir</button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</section>
</body>
</html>
