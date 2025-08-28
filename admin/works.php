<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';
require_once '../includes/models.php';

requireLogin();

$workModel = new WorkModel();
$categoryModel = new CategoryModel();

$success = '';
$error = '';
$editWork = null;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
            case 'update':
                $data = [
                    'title' => sanitize($_POST['title']),
                    'description' => sanitize($_POST['description']),
                    'long_description' => $_POST['long_description'], // Allow HTML
                    'image_url' => sanitize($_POST['image_url']),
                    'category' => sanitize($_POST['category']),
                    'tags' => isset($_POST['tags']) ? explode(',', $_POST['tags']) : [],
                    'link' => sanitize($_POST['link']),
                    'image_style' => sanitize($_POST['image_style']),
                    'place' => sanitize($_POST['place'])
                ];
                
                if ($_POST['action'] === 'create') {
                    if ($workModel->create($data)) {
                        $success = 'Work created successfully!';
                    } else {
                        $error = 'Failed to create work.';
                    }
                } else {
                    $id = intval($_POST['work_id']);
                    if ($workModel->update($id, $data)) {
                        $success = 'Work updated successfully!';
                    } else {
                        $error = 'Failed to update work.';
                    }
                }
                break;
                
            case 'delete':
                $id = intval($_POST['work_id']);
                if ($workModel->delete($id)) {
                    $success = 'Work deleted successfully!';
                } else {
                    $error = 'Failed to delete work.';
                }
                break;
        }
    }
}

// Handle edit request
if (isset($_GET['edit'])) {
    $editId = intval($_GET['edit']);
    $editWork = $workModel->getById($editId);
}

$works = $workModel->getAll();
$categories = $categoryModel->getAll('work');
$pageTitle = 'Works Management - Admin';
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
                        <h1 class="h2">Works Management</h1>
                        <div class="btn-toolbar mb-2 mb-md-0">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#workModal">
                                <i class="fas fa-plus me-1"></i>Add New Work
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

                <!-- Works Table -->
                <div class="card shadow">
                    <div class="card-header">
                        <h5 class="mb-0">All Works</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($works): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Image</th>
                                            <th>Title</th>
                                            <th>Category</th>
                                            <th>Created</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($works as $work): ?>
                                            <tr>
                                                <td>
                                                    <?php if ($work['image_url']): ?>
                                                        <img src="<?php echo htmlspecialchars($work['image_url']); ?>" 
                                                             alt="<?php echo htmlspecialchars($work['title']); ?>"
                                                             class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                                    <?php else: ?>
                                                        <div class="bg-light d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                                            <i class="fas fa-image text-muted"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($work['title']); ?></strong>
                                                    <br>
                                                    <small class="text-muted"><?php echo truncateText($work['description'], 50); ?></small>
                                                </td>
                                                <td><span class="badge bg-primary"><?php echo htmlspecialchars($work['category']); ?></span></td>
                                                <td><?php echo formatDate($work['created_at']); ?></td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="../work-detail.php?id=<?php echo $work['id']; ?>" 
                                                           class="btn btn-outline-info" target="_blank" title="View">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="?edit=<?php echo $work['id']; ?>" 
                                                           class="btn btn-outline-primary" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-outline-danger" 
                                                                onclick="deleteWork(<?php echo $work['id']; ?>)" title="Delete">
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
                            <div class="text-center py-5">
                                <i class="fas fa-briefcase fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No works found</h5>
                                <p class="text-muted">Create your first work to get started.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Work Modal -->
    <div class="modal fade" id="workModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" action="">
                    <div class="modal-header">
                        <h5 class="modal-title"><?php echo $editWork ? 'Edit Work' : 'Add New Work'; ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="<?php echo $editWork ? 'update' : 'create'; ?>">
                        <?php if ($editWork): ?>
                            <input type="hidden" name="work_id" value="<?php echo $editWork['id']; ?>">
                        <?php endif; ?>

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="title" class="form-label">Title *</label>
                                <input type="text" class="form-control" id="title" name="title" 
                                       value="<?php echo $editWork ? htmlspecialchars($editWork['title']) : ''; ?>" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="category" class="form-label">Category *</label>
                                <select class="form-select" id="category" name="category" required>
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?php echo htmlspecialchars($cat['name']); ?>"
                                                <?php echo ($editWork && $editWork['category'] === $cat['name']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($cat['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Short Description *</label>
                            <textarea class="form-control" id="description" name="description" rows="3" required><?php echo $editWork ? htmlspecialchars($editWork['description']) : ''; ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="long_description" class="form-label">Detailed Description</label>
                            <textarea class="form-control" id="long_description" name="long_description" rows="6"><?php echo $editWork ? htmlspecialchars($editWork['long_description']) : ''; ?></textarea>
                            <small class="text-muted">HTML tags are allowed</small>
                        </div>

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="image_url" class="form-label">Image URL</label>
                                <input type="url" class="form-control" id="image_url" name="image_url" 
                                       value="<?php echo $editWork ? htmlspecialchars($editWork['image_url']) : ''; ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="image_style" class="form-label">Image Style</label>
                                <select class="form-select" id="image_style" name="image_style">
                                    <option value="cover" <?php echo ($editWork && $editWork['image_style'] === 'cover') ? 'selected' : ''; ?>>Cover</option>
                                    <option value="contain" <?php echo ($editWork && $editWork['image_style'] === 'contain') ? 'selected' : ''; ?>>Contain</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="link" class="form-label">Project Link</label>
                                <input type="url" class="form-control" id="link" name="link" 
                                       value="<?php echo $editWork ? htmlspecialchars($editWork['link']) : ''; ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="place" class="form-label">Location</label>
                                <input type="text" class="form-control" id="place" name="place" 
                                       value="<?php echo $editWork ? htmlspecialchars($editWork['place']) : ''; ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="tags" class="form-label">Tags</label>
                            <input type="text" class="form-control" id="tags" name="tags" 
                                   value="<?php echo $editWork && $editWork['tags'] ? implode(', ', $editWork['tags']) : ''; ?>"
                                   placeholder="Separate tags with commas">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i><?php echo $editWork ? 'Update Work' : 'Create Work'; ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Form -->
    <form id="deleteForm" method="POST" action="" style="display: none;">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="work_id" id="deleteWorkId">
    </form>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function deleteWork(workId) {
            if (confirm('Are you sure you want to delete this work? This action cannot be undone.')) {
                document.getElementById('deleteWorkId').value = workId;
                document.getElementById('deleteForm').submit();
            }
        }

        <?php if ($editWork): ?>
            // Show modal if editing
            document.addEventListener('DOMContentLoaded', function() {
                var modal = new bootstrap.Modal(document.getElementById('workModal'));
                modal.show();
            });
        <?php endif; ?>
    </script>
</body>
</html>