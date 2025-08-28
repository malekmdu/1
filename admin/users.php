<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';
require_once '../includes/models.php';

requireLogin();
requireAdmin(); // Only admins can manage users

$userModel = new UserModel();

$success = '';
$error = '';
$editUser = null;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $data = [
                    'name' => sanitize($_POST['name']),
                    'email' => sanitize($_POST['email']),
                    'password' => $_POST['password'],
                    'role' => sanitize($_POST['role']),
                    'is_active' => isset($_POST['is_active']) ? 1 : 0
                ];
                
                // Validate email
                if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                    $error = 'Invalid email address.';
                } elseif (strlen($data['password']) < 6) {
                    $error = 'Password must be at least 6 characters long.';
                } elseif ($userModel->emailExists($data['email'])) {
                    $error = 'Email address already exists.';
                } else {
                    if ($userModel->create($data)) {
                        $success = 'User created successfully!';
                    } else {
                        $error = 'Failed to create user.';
                    }
                }
                break;
                
            case 'update':
                $id = intval($_POST['user_id']);
                $data = [
                    'name' => sanitize($_POST['name']),
                    'email' => sanitize($_POST['email']),
                    'role' => sanitize($_POST['role']),
                    'is_active' => isset($_POST['is_active']) ? 1 : 0
                ];
                
                // Add password if provided
                if (!empty($_POST['password'])) {
                    if (strlen($_POST['password']) < 6) {
                        $error = 'Password must be at least 6 characters long.';
                        break;
                    }
                    $data['password'] = $_POST['password'];
                }
                
                // Validate email
                if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                    $error = 'Invalid email address.';
                } elseif ($userModel->emailExistsExcept($data['email'], $id)) {
                    $error = 'Email address already exists.';
                } else {
                    if ($userModel->update($id, $data)) {
                        $success = 'User updated successfully!';
                    } else {
                        $error = 'Failed to update user.';
                    }
                }
                break;
                
            case 'delete':
                $id = intval($_POST['user_id']);
                if ($id === $_SESSION['user_id']) {
                    $error = 'You cannot delete your own account.';
                } else {
                    if ($userModel->delete($id)) {
                        $success = 'User deleted successfully!';
                    } else {
                        $error = 'Failed to delete user.';
                    }
                }
                break;
                
            case 'toggle_status':
                $id = intval($_POST['user_id']);
                if ($id === $_SESSION['user_id']) {
                    $error = 'You cannot deactivate your own account.';
                } else {
                    if ($userModel->toggleStatus($id)) {
                        $success = 'User status updated successfully!';
                    } else {
                        $error = 'Failed to update user status.';
                    }
                }
                break;
        }
    }
}

// Handle edit request
if (isset($_GET['edit'])) {
    $editId = intval($_GET['edit']);
    $editUser = $userModel->getById($editId);
}

$users = $userModel->getAll();
$pageTitle = 'Users Management - Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'templates/admin-navbar.php'; ?>

    <div class="container-fluid">
        <div class="row">
            <?php include 'templates/admin-sidebar.php'; ?>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="admin-header">
                    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center">
                        <h1 class="h2">Users Management</h1>
                        <div class="btn-toolbar mb-2 mb-md-0">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#userModal">
                                <i class="fas fa-plus me-1"></i>Add New User
                            </button>
                        </div>
                    </div>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        <?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>

                <!-- Users Table -->
                <div class="card shadow">
                    <div class="card-header">
                        <h5 class="mb-0">All Users</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Last Login</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user): ?>
                                        <tr class="<?php echo !$user['is_active'] ? 'table-secondary' : ''; ?>">
                                            <td>
                                                <strong><?php echo htmlspecialchars($user['name']); ?></strong>
                                                <?php if ($user['id'] === $_SESSION['user_id']): ?>
                                                    <span class="badge bg-info ms-1">You</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                                            <td>
                                                <span class="badge <?php echo $user['role'] === 'admin' ? 'bg-danger' : 'bg-primary'; ?>">
                                                    <?php echo ucfirst($user['role']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge <?php echo $user['is_active'] ? 'bg-success' : 'bg-secondary'; ?>">
                                                    <?php echo $user['is_active'] ? 'Active' : 'Inactive'; ?>
                                                </span>
                                            </td>
                                            <td><?php echo formatDate($user['created_at']); ?></td>
                                            <td><?php echo $user['last_login'] ? formatDate($user['last_login']) : 'Never'; ?></td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="?edit=<?php echo $user['id']; ?>" 
                                                       class="btn btn-outline-primary" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <?php if ($user['id'] !== $_SESSION['user_id']): ?>
                                                        <button type="button" class="btn btn-outline-warning" 
                                                                onclick="toggleStatus(<?php echo $user['id']; ?>)" 
                                                                title="<?php echo $user['is_active'] ? 'Deactivate' : 'Activate'; ?>">
                                                            <i class="fas fa-<?php echo $user['is_active'] ? 'pause' : 'play'; ?>"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-outline-danger" 
                                                                onclick="deleteUser(<?php echo $user['id']; ?>)" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- User Modal -->
    <div class="modal fade" id="userModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="">
                    <div class="modal-header">
                        <h5 class="modal-title"><?php echo $editUser ? 'Edit User' : 'Add New User'; ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="<?php echo $editUser ? 'update' : 'create'; ?>">
                        <?php if ($editUser): ?>
                            <input type="hidden" name="user_id" value="<?php echo $editUser['id']; ?>">
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name *</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?php echo $editUser ? htmlspecialchars($editUser['name']) : ''; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address *</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo $editUser ? htmlspecialchars($editUser['email']) : ''; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password <?php echo $editUser ? '(leave blank to keep current)' : '*'; ?></label>
                            <input type="password" class="form-control" id="password" name="password" 
                                   <?php echo !$editUser ? 'required' : ''; ?>>
                            <small class="text-muted">Minimum 6 characters</small>
                        </div>

                        <div class="mb-3">
                            <label for="role" class="form-label">Role *</label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="">Select Role</option>
                                <option value="admin" <?php echo ($editUser && $editUser['role'] === 'admin') ? 'selected' : ''; ?>>Administrator</option>
                                <option value="editor" <?php echo ($editUser && $editUser['role'] === 'editor') ? 'selected' : ''; ?>>Editor</option>
                            </select>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" 
                                   <?php echo (!$editUser || $editUser['is_active']) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="is_active">
                                Active User
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i><?php echo $editUser ? 'Update User' : 'Create User'; ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Action Forms -->
    <form id="actionForm" method="POST" action="" style="display: none;">
        <input type="hidden" name="action" id="formAction">
        <input type="hidden" name="user_id" id="formUserId">
    </form>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleStatus(userId) {
            if (confirm('Are you sure you want to toggle this user\'s status?')) {
                document.getElementById('formAction').value = 'toggle_status';
                document.getElementById('formUserId').value = userId;
                document.getElementById('actionForm').submit();
            }
        }
        
        function deleteUser(userId) {
            if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
                document.getElementById('formAction').value = 'delete';
                document.getElementById('formUserId').value = userId;
                document.getElementById('actionForm').submit();
            }
        }

        <?php if ($editUser): ?>
            // Show modal if editing
            document.addEventListener('DOMContentLoaded', function() {
                var modal = new bootstrap.Modal(document.getElementById('userModal'));
                modal.show();
            });
        <?php endif; ?>
    </script>
</body>
</html>