<?php 
include '../db.php'; 
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// // Security Check
// if(!isset($_SESSION['user_id'])) { header("Location: ../login.php"); exit(); }

$show_success = false;
if(isset($_GET['msg']) && $_GET['msg'] == 'success') $show_success = true;

// Fetch current header settings
$h_res = mysqli_query($conn, "SELECT * FROM header_settings LIMIT 1");
$header = mysqli_fetch_assoc($h_res);

if(isset($_POST['update_header'])) {
    $l_first = mysqli_real_escape_string($conn, $_POST['logo_first']);
    $l_second = mysqli_real_escape_string($conn, $_POST['logo_second']);
    $show_auth = isset($_POST['show_auth']) ? 1 : 0;

    // Update or Insert Logo Settings
    if($header) {
        mysqli_query($conn, "UPDATE header_settings SET logo_first='$l_first', logo_second='$l_second', show_auth='$show_auth' WHERE id=".$header['id']);
    } else {
        mysqli_query($conn, "INSERT INTO header_settings (logo_first, logo_second, show_auth) VALUES ('$l_first', '$l_second', '$show_auth')");
    }

    // Refresh Navigation Links
    mysqli_query($conn, "TRUNCATE TABLE nav_links");
    if(!empty($_POST['nav_names'])) {
        foreach($_POST['nav_names'] as $name) {
            if(!empty(trim($name))) {
                $clean_name = mysqli_real_escape_string($conn, trim($name));
                // Slug generation (Home -> #home)
                $path = "#" . strtolower(str_replace(' ', '-', $clean_name));
                mysqli_query($conn, "INSERT INTO nav_links (link_name, link_path) VALUES ('$clean_name', '$path')");
            }
        }
    }
    header("Location: manage_header.php?msg=success");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Header | Admin Panel</title>
    <link rel="icon" href="../pics/icon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <style>
        :root { --p-blue: #0ea5e9; --p-dark: #0f172a; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f1f5f9; color: var(--p-dark); }
        
        .admin-card { background: white; border-radius: 30px; box-shadow: 0 20px 50px rgba(0,0,0,0.05); padding: 40px; border: none; }
        
        .logo-preview { 
            background: #fff; padding: 15px 25px; border-radius: 15px; 
            display: inline-block; border: 2px dashed #e2e8f0; margin-bottom: 25px;
        }
        .logo-text { font-size: 1.5rem; font-weight: 800; letter-spacing: -1px; }

        .custom-input { 
            border-radius: 14px; padding: 14px; border: 2px solid #f1f5f9; 
            background: #f8fafc; font-weight: 600; transition: 0.3s; 
        }
        .custom-input:focus { border-color: var(--p-blue); background: #fff; box-shadow: none; }

        .link-row { 
            background: #fff; border: 1px solid #e2e8f0; padding: 10px; 
            border-radius: 16px; margin-bottom: 10px; transition: 0.2s;
        }
        .link-row:hover { border-color: var(--p-blue); transform: translateX(5px); }

        .success-alert { 
            background: #10b981; color: white; border-radius: 20px; 
            padding: 15px 30px; position: fixed; top: 30px; right: 30px; 
            z-index: 9999; font-weight: 700; box-shadow: 0 15px 30px rgba(16,185,129,0.3); 
        }
    </style>
</head>
<body>

    <?php if($show_success): ?>
    <div class="success-alert animate__animated animate__backInRight">
        <i class="fa-solid fa-circle-check me-2"></i> Header Settings Updated!
    </div>
    <script>setTimeout(() => { window.location.href = 'manage_header.php'; }, 3000);</script>
    <?php endif; ?>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                
                <div class="d-flex align-items-center mb-4 px-2">
                    <a href="admin_panel.php" class="btn btn-white shadow-sm rounded-circle p-3 me-3 text-dark border">
                        <i class="fa fa-arrow-left"></i>
                    </a>
                    <div>
                        <h2 class="fw-800 mb-0">Header & <span class="text-primary">Nav</span></h2>
                        <p class="text-muted small fw-600 mb-0">Control your branding and menu links.</p>
                    </div>
                </div>

                <div class="admin-card">
                    <form method="POST">
                        
                        <div class="mb-5 text-center">
                            <label class="form-label d-block fw-800 text-muted small text-uppercase mb-3">Live Branding Preview</label>
                            <div class="logo-preview">
                                <span class="logo-text text-dark" id="prev_first"><?= $header['logo_first'] ?? 'Pashu' ?></span><span class="logo-text text-primary" id="prev_second"><?= $header['logo_second'] ?? 'Mandi' ?></span>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6 text-start">
                                    <label class="small fw-bold mb-2">Logo First Part</label>
                                    <input type="text" name="logo_first" id="in_first" class="form-control custom-input" value="<?= $header['logo_first'] ?? 'Pashu' ?>" required>
                                </div>
                                <div class="col-md-6 text-start">
                                    <label class="small fw-bold mb-2">Logo Second Part (Colored)</label>
                                    <input type="text" name="logo_second" id="in_second" class="form-control custom-input" value="<?= $header['logo_second'] ?? 'Mandi' ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <label class="fw-800 text-muted small text-uppercase">Menu Links</label>
                                <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold" onclick="addRow()">
                                    <i class="fa fa-plus me-1"></i> Add Link
                                </button>
                            </div>
                            
                            <div id="links-container">
                                <?php 
                                $navs = mysqli_query($conn, "SELECT * FROM nav_links");
                                if(mysqli_num_rows($navs) > 0):
                                    while($row = mysqli_fetch_assoc($navs)): ?>
                                        <div class="d-flex gap-2 link-row align-items-center animate__animated animate__fadeIn">
                                            <div class="bg-light rounded-pill px-3 py-1 small fw-800 text-muted">Link</div>
                                            <input type="text" name="nav_names[]" class="form-control border-0 bg-transparent fw-bold" value="<?= $row['link_name'] ?>">
                                            <button type="button" class="btn text-danger btn-sm" onclick="this.parentElement.remove()">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </div>
                                    <?php endwhile; 
                                else: ?>
                                    <div class="text-center py-4 bg-light rounded-4 border-2 border-dashed border-secondary opacity-50">
                                        <p class="small fw-600 mb-0">No links added yet.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="form-check form-switch mb-5 p-3 px-5 border rounded-4 d-flex justify-content-between align-items-center">
                            <div>
                                <label class="form-check-label fw-800 d-block" for="auth">Authentication UI</label>
                                <small class="text-muted">Show Login/Signup buttons in header</small>
                            </div>
                            <input class="form-check-input h4 mb-0" type="checkbox" name="show_auth" id="auth" <?= ($header['show_auth'] ?? 1) ? 'checked' : '' ?>>
                        </div>

                        <button type="submit" name="update_header" class="btn btn-dark w-100 py-3 rounded-pill fw-800 shadow-lg">
                            APPLY CHANGES TO HEADER
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Real-time Logo Preview
        const inFirst = document.getElementById('in_first');
        const inSecond = document.getElementById('in_second');
        const prevFirst = document.getElementById('prev_first');
        const prevSecond = document.getElementById('prev_second');

        inFirst.addEventListener('input', () => prevFirst.innerText = inFirst.value);
        inSecond.addEventListener('input', () => prevSecond.innerText = inSecond.value);

        function addRow() {
            const container = document.getElementById('links-container');
            // Remove empty state message if exists
            if(container.innerText.includes('No links added')) container.innerHTML = '';
            
            const div = document.createElement('div');
            div.className = 'd-flex gap-2 link-row align-items-center animate__animated animate__fadeInUp';
            div.innerHTML = `
                <div class="bg-light rounded-pill px-3 py-1 small fw-800 text-muted">New</div>
                <input type="text" name="nav_names[]" class="form-control border-0 bg-transparent fw-bold" placeholder="e.g. Services">
                <button type="button" class="btn text-danger btn-sm" onclick="this.parentElement.remove()">
                    <i class="fa-solid fa-trash-can"></i>
                </button>`;
            container.appendChild(div);
        }
    </script>
</body>
</html>