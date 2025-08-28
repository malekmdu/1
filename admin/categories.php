<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';
require_once '../includes/models.php';

requireLogin();

$categoryModel = new CategoryModel();

$success = '';
$error = '';
$editCategory = null;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $data = [
                    'name' => sanitize($_POST['name']),
                    'type' => sanitize($_POST['type']),
                    'description' => sanitize($_POST['description'])
                ];
                
                if ($categoryModel->create($data)) {
                    $success = 'Category created successfully!';
                } else {
                    $error = 'Failed to create category.';
                }
                break;
                
            case 'update':
                $id = intval($_POST['category_id']);
                $data = [
                    'name' => sanitize($_POST['name']),
                    'type' => sanitize($_POST['type']),
                    'description' => sanitize($_POST['description'])
                ];
                
                if ($categoryModel->update($id, $data)) {
                    $success = 'Category updated successfully!';
                } else {
                    $error = 'Failed to update category.';
                }
                break;
                
            case 'delete':
                $id = intval($_POST['category_id']);
                if ($categoryModel->delete($id)) {
                    $success = 'Category deleted successfully!';
                } else {
                    $error = 'Failed to delete category. It may be in use by existing content.';
                }
                break;
        }
    }
}

// Handle edit request
if (isset($_GET['edit'])) {
    $editId = intval($_GET['edit']);
    $editCategory = $categoryModel->getById($editId);
}

$workCategories = $categoryModel->getAll('work');
$blogCategories = $categoryModel->getAll('blog');
$pageTitle = 'Categories Management - Admin';
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
                        <h1 class="h2">Categories Management</h1>
                        <div class="btn-toolbar mb-2 mb-md-0">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#categoryModal">
                                <i class="fas fa-plus me-1"></i>Add New Category
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

                <div class="row">
                    <!-- Work Categories -->
                    <div class="col-lg-6 mb-4">
                        <div class="card shadow">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-briefcase me-2"></i>Work Categories</h5>
                            </div>
                            <div class="card-body">
                                <?php if ($workCategories): ?>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Description</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($workCategories as $category): ?>
                                                    <tr>
                                                        <td><strong><?php echo htmlspecialchars($category['name']); ?></strong></td>
                                                        <td><?php echo htmlspecialchars($category['description'] ?: 'No description'); ?></td>
                                                        <td>
                                                            <div class="btn-group btn-group-sm">
                                                                <a href="?edit=<?php echo $category['id']; ?>" 
                                                                   class="btn btn-outline-primary" title="Edit">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                                <button type="button" class="btn btn-outline-danger" 
                                                                        onclick="deleteCategory(<?php echo $category['id']; ?>)" title="Delete">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted">No work categories found.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Blog Categories -->
                    <div class="col-lg-6 mb-4">
                        <div class="card shadow">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-blog me-2"></i>Blog Categories</h5>
                            </div>
                            <div class="card-body">
                                <?php if ($blogCategories): ?>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Description</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($blogCategories as $category): ?>
                                                    <tr>
                                                        <td><strong><?php echo htmlspecialchars($category['name']); ?></strong></td>
                                                        <td><?php echo htmlspecialchars($category['description'] ?: 'No description'); ?></td>
                                                        <td>
                                                            <div class="btn-group btn-group-sm">
                                                                <a href="?edit=<?php echo $category['id']; ?>" 
                                                                   class="btn btn-outline-primary" title="Edit">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                                <button type="button" class="btn btn-outline-danger" 
                                                                        onclick="deleteCategory(<?php echo $category['id']; ?>)" title="Delete">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted">No blog categories found.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Category Modal -->
    <div class="modal fade" id="categoryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="">
                    <div class="modal-header">
                        <h5 class="modal-title"><?php echo $editCategory ? 'Edit Category' : 'Add New Category'; ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="<?php echo $editCategory ? 'update' : 'create'; ?>">
                        <?php if ($editCategory): ?>
                            <input type="hidden" name="category_id" value="<?php echo $editCategory['id']; ?>">
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="name" class="form-label">Category Name *</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?php echo $editCategory ? htmlspecialchars($editCategory['name']) : ''; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="type" class="form-label">Type *</label>
                            <select class="form-select" id="type" name="type" required>
                                <option value="">Select Type</option>
                                <option value="work" <?php echo ($editCategory && $editCategory['type'] === 'work') ? 'selected' : ''; ?>>Work</option>
                                <option value="blog" <?php echo ($editCategory && $editCategory['type'] === 'blog') ? 'selected' : ''; ?>>Blog</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"><?php echo $editCategory ? htmlspecialchars($editCategory['description']) : ''; ?></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i><?php echo $editCategory ? 'Update Category' : 'Create Category'; ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Form -->
    <form id="deleteForm" method="POST" action="" style="display: none;">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="category_id" id="deleteCategoryId">
    </form>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function deleteCategory(categoryId) {
            if (confirm('Are you sure you want to delete this category? This action cannot be undone.')) {
                document.getElementById('deleteCategoryId').value = categoryId;
                document.getElementById('deleteForm').submit();
            }
        }

        <?php if ($editCategory): ?>
            // Show modal if editing
            document.addEventListener('DOMContentLoaded', function() {
                var modal = new bootstrap.Modal(document.getElementById('categoryModal'));
                modal.show();
            });
        <?php endif; ?>
    </script>
</body>
</html>