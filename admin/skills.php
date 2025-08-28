<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';
require_once '../includes/models.php';

requireLogin();

$skillModel = new SkillModel();

$success = '';
$error = '';
$editSkill = null;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create_category':
                $data = [
                    'category' => sanitize($_POST['category'])
                ];
                
                if ($skillModel->createCategory($data)) {
                    $success = 'Skill category created successfully!';
                } else {
                    $error = 'Failed to create skill category.';
                }
                break;
                
            case 'create_item':
                $data = [
                    'skill_category_id' => intval($_POST['skill_category_id']),
                    'name' => sanitize($_POST['name']),
                    'percentage' => intval($_POST['percentage'])
                ];
                
                if ($skillModel->createItem($data)) {
                    $success = 'Skill item created successfully!';
                } else {
                    $error = 'Failed to create skill item.';
                }
                break;
                
            case 'update_item':
                $id = intval($_POST['skill_item_id']);
                $data = [
                    'name' => sanitize($_POST['name']),
                    'percentage' => intval($_POST['percentage'])
                ];
                
                if ($skillModel->updateItem($id, $data)) {
                    $success = 'Skill item updated successfully!';
                } else {
                    $error = 'Failed to update skill item.';
                }
                break;
                
            case 'delete_category':
                $id = intval($_POST['category_id']);
                if ($skillModel->deleteCategory($id)) {
                    $success = 'Skill category deleted successfully!';
                } else {
                    $error = 'Failed to delete skill category.';
                }
                break;
                
            case 'delete_item':
                $id = intval($_POST['item_id']);
                if ($skillModel->deleteItem($id)) {
                    $success = 'Skill item deleted successfully!';
                } else {
                    $error = 'Failed to delete skill item.';
                }
                break;
        }
    }
}

// Handle edit request
if (isset($_GET['edit_item'])) {
    $editId = intval($_GET['edit_item']);
    $editSkill = $skillModel->getItemById($editId);
}

$skills = $skillModel->getAllWithItems();
$pageTitle = 'Skills Management - Admin';
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
                        <h1 class="h2">Skills Management</h1>
                        <div class="btn-toolbar mb-2 mb-md-0">
                            <div class="btn-group me-2">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#categoryModal">
                                    <i class="fas fa-plus me-1"></i>Add Category
                                </button>
                                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#skillModal">
                                    <i class="fas fa-plus me-1"></i>Add Skill
                                </button>
                            </div>
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

                <!-- Skills Display -->
                <?php if ($skills): ?>
                    <div class="row">
                        <?php foreach ($skills as $skillCategory): ?>
                            <div class="col-lg-6 mb-4">
                                <div class="card shadow">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0"><?php echo htmlspecialchars($skillCategory['category']); ?></h5>
                                        <button type="button" class="btn btn-outline-danger btn-sm" 
                                                onclick="deleteCategory(<?php echo $skillCategory['id']; ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    <div class="card-body">
                                        <?php if ($skillCategory['items']): ?>
                                            <?php foreach ($skillCategory['items'] as $skill): ?>
                                                <div class="mb-3">
                                                    <div class="d-flex justify-content-between mb-1">
                                                        <span><?php echo htmlspecialchars($skill['name']); ?></span>
                                                        <div>
                                                            <span class="me-2"><?php echo $skill['percentage']; ?>%</span>
                                                            <a href="?edit_item=<?php echo $skill['id']; ?>" class="btn btn-sm btn-outline-primary me-1">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                                                    onclick="deleteItem(<?php echo $skill['id']; ?>)">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="progress">
                                                        <div class="progress-bar" role="progressbar" 
                                                             style="width: <?php echo $skill['percentage']; ?>%"
                                                             aria-valuenow="<?php echo $skill['percentage']; ?>" 
                                                             aria-valuemin="0" aria-valuemax="100">
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <p class="text-muted">No skills in this category yet.</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="card shadow">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-cogs fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No skills found</h5>
                            <p class="text-muted">Create your first skill category to get started.</p>
                        </div>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <!-- Category Modal -->
    <div class="modal fade" id="categoryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Skill Category</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="create_category">
                        
                        <div class="mb-3">
                            <label for="category" class="form-label">Category Name *</label>
                            <input type="text" class="form-control" id="category" name="category" 
                                   placeholder="e.g., Programming Languages, GIS Software" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Create Category
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Skill Modal -->
    <div class="modal fade" id="skillModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="">
                    <div class="modal-header">
                        <h5 class="modal-title"><?php echo $editSkill ? 'Edit Skill' : 'Add New Skill'; ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="<?php echo $editSkill ? 'update_item' : 'create_item'; ?>">
                        <?php if ($editSkill): ?>
                            <input type="hidden" name="skill_item_id" value="<?php echo $editSkill['id']; ?>">
                        <?php endif; ?>

                        <?php if (!$editSkill): ?>
                            <div class="mb-3">
                                <label for="skill_category_id" class="form-label">Category *</label>
                                <select class="form-select" id="skill_category_id" name="skill_category_id" required>
                                    <option value="">Select Category</option>
                                    <?php foreach ($skills as $cat): ?>
                                        <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['category']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="name" class="form-label">Skill Name *</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?php echo $editSkill ? htmlspecialchars($editSkill['name']) : ''; ?>" 
                                   placeholder="e.g., Python, ArcGIS" required>
                        </div>

                        <div class="mb-3">
                            <label for="percentage" class="form-label">Proficiency Level (%) *</label>
                            <input type="number" class="form-control" id="percentage" name="percentage" 
                                   value="<?php echo $editSkill ? $editSkill['percentage'] : ''; ?>" 
                                   min="0" max="100" required>
                            <small class="text-muted">Enter a value between 0 and 100</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i><?php echo $editSkill ? 'Update Skill' : 'Add Skill'; ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Action Forms -->
    <form id="deleteForm" method="POST" action="" style="display: none;">
        <input type="hidden" name="action" id="deleteAction">
        <input type="hidden" name="category_id" id="deleteCategoryId">
        <input type="hidden" name="item_id" id="deleteItemId">
    </form>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function deleteCategory(categoryId) {
            if (confirm('Are you sure you want to delete this category and all its skills? This action cannot be undone.')) {
                document.getElementById('deleteAction').value = 'delete_category';
                document.getElementById('deleteCategoryId').value = categoryId;
                document.getElementById('deleteForm').submit();
            }
        }
        
        function deleteItem(itemId) {
            if (confirm('Are you sure you want to delete this skill? This action cannot be undone.')) {
                document.getElementById('deleteAction').value = 'delete_item';
                document.getElementById('deleteItemId').value = itemId;
                document.getElementById('deleteForm').submit();
            }
        }

        <?php if ($editSkill): ?>
            // Show modal if editing
            document.addEventListener('DOMContentLoaded', function() {
                var modal = new bootstrap.Modal(document.getElementById('skillModal'));
                modal.show();
            });
        <?php endif; ?>
    </script>
</body>
</html>