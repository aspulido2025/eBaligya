<?php
namespace App\Classes;

class RBAC {
    private DB $db;
    private int $userId;
    private ?int $activeRoleId = null;
    private array $permissions = [];

    public function __construct(DB $db, int $userId, ?int $activeRoleId = null) {
        $this->db = $db;
        $this->userId = $userId;

        // Load default role if no active role given
        if ($activeRoleId === null) {
            $this->activeRoleId = $this->getDefaultRoleId();
        } else {
            $this->activeRoleId = $activeRoleId;
        }

        if ($this->activeRoleId !== null) {
            $this->loadPermissions();
            $_SESSION['active_role_id'] = $this->activeRoleId;
        }
    }

    /**
     * Fetch the default role_id for this user.
     */
    private function getDefaultRoleId(): ?int {
        $row = $this->db->fetch(
            "SELECT role_id 
             FROM rbac_user_roles 
             WHERE user_id = ? AND is_default = 1 
             LIMIT 1",
            [$this->userId],
            [],
            'row'
        );
        return $row['role_id'] ?? null;
    }

    /**
     * Get all roles for this user.
     */
    public function getUserRoles(): array {
        $sql = "SELECT r.id, r.access, ur.is_default
                FROM rbac_user_roles ur
                JOIN rbac_roles r ON ur.role_id = r.id
                WHERE ur.user_id = ?";
        return $this->db->fetch($sql, [$this->userId], [], 'all');
    }

    /**
     * Set the active role and reload permissions.
     */
    public function setActiveRole(int $roleId): void {
        $this->activeRoleId = $roleId;
        $this->loadPermissions();
        $_SESSION['active_role_id'] = $roleId;
    }

    /**
     * Return currently active role_id.
     */
    public function getActiveRole(): ?int {
        return $this->activeRoleId;
    }

    /**
     * Load permissions for the active role.
     */
    private function loadPermissions(): void {
        $sql = "SELECT p.operation
                FROM rbac_role_permissions rp
                JOIN rbac_permissions p ON rp.permission_id = p.id
                WHERE rp.role_id = ?";
        $this->permissions = $this->db->fetch($sql, [$this->activeRoleId], [], 'col');
    }

    /**
     * Check if user has permission.
     */
    public function can(string $permission): bool {
        return in_array($permission, $this->permissions, true);
    }

    /**
     * Enforce permission or throw exception.
     */
    public function enforce(string $permission): bool {
        if (!$this->can($permission)) {
            throw new RBACException("Access Denied: Missing Permission [$permission]");
        }
        return true;
    }


    /**
     * List all permissions for active role.
     */
    public function listPermissions(): array {
        return $this->permissions;
    }
}


//===============================================================
// use App\Classes\RBACException;

// try {
//     $rbac->enforce('admin_panel');
// } catch (RBACException $e) {
//     // Handle access denied (log, redirect, flash message, etc.)
// } catch (\PHPMailer\PHPMailer\Exception $e) {
//     // Handle email-specific errors
// } catch (\Exception $e) {
//     // Catch-all for anything else
// }
 