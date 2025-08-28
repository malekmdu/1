<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';
require_once '../includes/models.php';

requireLogin();

$messageModel = new MessageModel();

$success = '';
$error = '';
$viewMessage = null;

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'mark_read':
                $id = intval($_POST['message_id']);
                if ($messageModel->markAsRead($id)) {
                    $success = 'Message marked as read.';
                } else {
                    $error = 'Failed to update message.';
                }
                break;
                
            case 'mark_unread':
                $id = intval($_POST['message_id']);
                if ($messageModel->markAsUnread($id)) {
                    $success = 'Message marked as unread.';
                } else {
                    $error = 'Failed to update message.';
                }
                break;
                
            case 'delete':
                $id = intval($_POST['message_id']);
                if ($messageModel->delete($id)) {
                    $success = 'Message deleted successfully.';
                } else {
                    $error = 'Failed to delete message.';
                }
                break;
        }
    }
}

// Handle view request
if (isset($_GET['view'])) {
    $viewId = intval($_GET['view']);
    $viewMessage = $messageModel->getById($viewId);
    if ($viewMessage && !$viewMessage['is_read']) {
        $messageModel->markAsRead($viewId);
    }
}

$messages = $messageModel->getAll();
$unreadCount = $messageModel->getUnreadCount();
$pageTitle = 'Messages Management - Admin';
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
                        <h1 class="h2">Messages Management 
                            <?php if ($unreadCount > 0): ?>
                                <span class="badge bg-danger"><?php echo $unreadCount; ?> unread</span>
                            <?php endif; ?>
                        </h1>
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

                <!-- Messages Table -->
                <div class="card shadow">
                    <div class="card-header">
                        <h5 class="mb-0">All Messages</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($messages): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Status</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Institution</th>
                                            <th>Received</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($messages as $message): ?>
                                            <tr class="<?php echo !$message['is_read'] ? 'table-warning' : ''; ?>">
                                                <td>
                                                    <?php if ($message['is_read']): ?>
                                                        <i class="fas fa-envelope-open text-muted" title="Read"></i>
                                                    <?php else: ?>
                                                        <i class="fas fa-envelope text-primary" title="Unread"></i>
                                                    <?php endif; ?>
                                                </td>
                                                <td><strong><?php echo htmlspecialchars($message['name']); ?></strong></td>
                                                <td>
                                                    <a href="mailto:<?php echo htmlspecialchars($message['email']); ?>">
                                                        <?php echo htmlspecialchars($message['email']); ?>
                                                    </a>
                                                </td>
                                                <td><?php echo htmlspecialchars($message['institution'] ?: 'N/A'); ?></td>
                                                <td><?php echo formatDate($message['created_at']); ?></td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="?view=<?php echo $message['id']; ?>" 
                                                           class="btn btn-outline-info" title="View">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <?php if ($message['is_read']): ?>
                                                            <button type="button" class="btn btn-outline-secondary" 
                                                                    onclick="markUnread(<?php echo $message['id']; ?>)" title="Mark as Unread">
                                                                <i class="fas fa-envelope"></i>
                                                            </button>
                                                        <?php else: ?>
                                                            <button type="button" class="btn btn-outline-primary" 
                                                                    onclick="markRead(<?php echo $message['id']; ?>)" title="Mark as Read">
                                                                <i class="fas fa-envelope-open"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                        <button type="button" class="btn btn-outline-danger" 
                                                                onclick="deleteMessage(<?php echo $message['id']; ?>)" title="Delete">
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
                                <i class="fas fa-envelope fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No messages found</h5>
                                <p class="text-muted">Messages from the contact form will appear here.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php if ($viewMessage): ?>
    <!-- Message View Modal -->
    <div class="modal fade show" id="messageModal" tabindex="-1" style="display: block;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Message from <?php echo htmlspecialchars($viewMessage['name']); ?></h5>
                    <a href="messages.php" class="btn-close"></a>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Name:</strong> <?php echo htmlspecialchars($viewMessage['name']); ?>
                        </div>
                        <div class="col-md-6">
                            <strong>Email:</strong> 
                            <a href="mailto:<?php echo htmlspecialchars($viewMessage['email']); ?>">
                                <?php echo htmlspecialchars($viewMessage['email']); ?>
                            </a>
                        </div>
                    </div>
                    
                    <?php if ($viewMessage['institution']): ?>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Institution:</strong> <?php echo htmlspecialchars($viewMessage['institution']); ?>
                            </div>
                            <?php if ($viewMessage['address']): ?>
                                <div class="col-md-6">
                                    <strong>Location:</strong> <?php echo htmlspecialchars($viewMessage['address']); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <strong>Received:</strong> <?php echo formatDate($viewMessage['created_at'], 'F j, Y \a\t g:i A'); ?>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Message:</strong>
                        <div class="border rounded p-3 bg-light mt-2">
                            <?php echo nl2br(htmlspecialchars($viewMessage['message'])); ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="mailto:<?php echo htmlspecialchars($viewMessage['email']); ?>?subject=Re: Your message&body=Hi <?php echo htmlspecialchars($viewMessage['name']); ?>,%0A%0AThank you for your message.%0A%0A" 
                       class="btn btn-primary">
                        <i class="fas fa-reply me-1"></i>Reply via Email
                    </a>
                    <a href="messages.php" class="btn btn-secondary">Close</a>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show"></div>
    <?php endif; ?>

    <!-- Action Forms -->
    <form id="actionForm" method="POST" action="" style="display: none;">
        <input type="hidden" name="action" id="formAction">
        <input type="hidden" name="message_id" id="formMessageId">
    </form>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function markRead(messageId) {
            document.getElementById('formAction').value = 'mark_read';
            document.getElementById('formMessageId').value = messageId;
            document.getElementById('actionForm').submit();
        }
        
        function markUnread(messageId) {
            document.getElementById('formAction').value = 'mark_unread';
            document.getElementById('formMessageId').value = messageId;
            document.getElementById('actionForm').submit();
        }
        
        function deleteMessage(messageId) {
            if (confirm('Are you sure you want to delete this message? This action cannot be undone.')) {
                document.getElementById('formAction').value = 'delete';
                document.getElementById('formMessageId').value = messageId;
                document.getElementById('actionForm').submit();
            }
        }
    </script>
</body>
</html>