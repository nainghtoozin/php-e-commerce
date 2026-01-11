<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $title ?? 'My Website' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background-color: #f8f9fa;
        }

        .sidebar {
            height: 100vh;
            position: fixed;
            width: 250px;
            background-color: #343a40;
        }

        .sidebar a {
            color: #fff;
            padding: 15px;
            display: block;
            text-decoration: none;
        }

        .sidebar a:hover {
            background-color: #495057;
        }

        .content {
            margin-left: 250px;
            padding: 2rem;
        }
    </style>
</head>

<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h4 class="text-white text-center py-3">📚 Bookshop Admin</h4>
        <a href="#">Dashboard</a>
        <a href="../_actions/categories/index.php">Categrories</a>
        <a href="../_actions/products/index.php">Products</a>
        <a href="#">Users</a>
        <a href="#">Settings</a>
    </div>

    <!-- Main Content -->
    <div class="content">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4">
            <div class="container-fluid">
                <span class="navbar-brand">Dashboard</span>
                <div class="d-flex">
                    <button class="btn btn-outline-secondary btn-sm me-2">Profile</button>
                    <button class="btn btn-outline-danger btn-sm">Logout</button>
                </div>
            </div>
        </nav>