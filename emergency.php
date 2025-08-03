<?php
/**
 * PrestaShop Emergency Administration Tool
 * Professional Emergency Management System
 * 
 * Colocar en la raíz de PrestaShop como emergency.php
 * ELIMINAR después del uso por seguridad
 */

session_start();

// Configuración
$CONFIG = [
    'password' => 'Admin2024!', // CAMBIAR OBLIGATORIAMENTE
    'session_timeout' => 3600,  // 1 hora
    'app_name' => 'PS Emergency Admin',
    'version' => '2.0'
];

// Verificar si existe PrestaShop
if (!file_exists('./config/config.inc.php')) {
    die('ERROR: Este script debe ejecutarse desde la raíz de PrestaShop');
}

require_once('./config/config.inc.php');

class PSEmergencyTool {
    private $config;
    private $authenticated = false;
    
    public function __construct($config) {
        $this->config = $config;
        $this->checkAuth();
    }
    
    private function checkAuth() {
        if (isset($_SESSION['ps_emergency_auth']) && 
            $_SESSION['ps_emergency_auth'] === true && 
            $_SESSION['ps_emergency_time'] > time() - $this->config['session_timeout']) {
            $this->authenticated = true;
            $_SESSION['ps_emergency_time'] = time();
        }
    }
    
    public function run() {
        if (!$this->authenticated) {
            $this->showLogin();
            return;
        }
        
        $action = $_GET['action'] ?? 'dashboard';
        $this->showHeader();
        
        switch ($action) {
            case 'dashboard':
                $this->showDashboard();
                break;
            case 'admins':
                $this->manageAdmins();
                break;
            case 'cache':
                $this->manageCache();
                break;
            case 'debug':
                $this->manageDebug();
                break;
            case 'maintenance':
                $this->manageMaintenance();
                break;
            case 'permissions':
                $this->managePermissions();
                break;
            case 'admin_folder':
                $this->manageAdminFolder();
                break;
            case 'disk_usage':
                $this->showDiskUsage();
                break;
            case 'system_info':
                $this->showSystemInfo();
                break;
            case 'logout':
                $this->logout();
                break;
            default:
                $this->showDashboard();
        }
        
        $this->showFooter();
    }
    
    private function showLogin() {
        if ($_POST['password'] ?? false) {
            if ($_POST['password'] === $this->config['password']) {
                $_SESSION['ps_emergency_auth'] = true;
                $_SESSION['ps_emergency_time'] = time();
                header('Location: ' . $_SERVER['PHP_SELF']);
                exit;
            } else {
                $error = 'Contraseña incorrecta';
            }
        }
        
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?= $this->config['app_name'] ?> - Login</title>
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body { 
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    min-height: 100vh;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                .login-container {
                    background: white;
                    padding: 40px;
                    border-radius: 15px;
                    box-shadow: 0 15px 35px rgba(0,0,0,0.1);
                    width: 100%;
                    max-width: 400px;
                    text-align: center;
                }
                .logo {
                    font-size: 2.5em;
                    color: #667eea;
                    margin-bottom: 10px;
                }
                h2 { color: #333; margin-bottom: 30px; }
                .form-group {
                    margin-bottom: 25px;
                    text-align: left;
                }
                label {
                    display: block;
                    margin-bottom: 8px;
                    color: #555;
                    font-weight: 600;
                }
                input[type="password"] {
                    width: 100%;
                    padding: 15px;
                    border: 2px solid #e1e8ed;
                    border-radius: 8px;
                    font-size: 16px;
                    transition: border-color 0.3s;
                }
                input[type="password"]:focus {
                    outline: none;
                    border-color: #667eea;
                }
                .btn {
                    width: 100%;
                    padding: 15px;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    color: white;
                    border: none;
                    border-radius: 8px;
                    font-size: 16px;
                    font-weight: 600;
                    cursor: pointer;
                    transition: transform 0.2s;
                }
                .btn:hover { transform: translateY(-2px); }
                .error {
                    background: #ff6b6b;
                    color: white;
                    padding: 12px;
                    border-radius: 8px;
                    margin-bottom: 20px;
                }
                .warning {
                    background: #ffeaa7;
                    color: #2d3436;
                    padding: 15px;
                    border-radius: 8px;
                    margin-top: 20px;
                    font-size: 14px;
                }
            </style>
        </head>
        <body>
            <div class="login-container">
                <div class="logo">🛡️</div>
                <h2><?= $this->config['app_name'] ?></h2>
                
                <?php if (isset($error)): ?>
                    <div class="error"><?= $error ?></div>
                <?php endif; ?>
                
                <form method="post">
                    <div class="form-group">
                        <label>Contraseña de Emergencia:</label>
                        <input type="password" name="password" required autofocus>
                    </div>
                    <button type="submit" class="btn">Acceder</button>
                </form>
                
                <div class="warning">
                    ⚠️ <strong>Herramienta de Emergencia</strong><br>
                    Solo usar cuando sea necesario y eliminar después del uso.
                </div>
            </div>
        </body>
        </html>
        <?php
    }
    
    private function showHeader() {
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?= $this->config['app_name'] ?></title>
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body {
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                    background: #f8f9fa;
                    color: #333;
                }
                .header {
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    color: white;
                    padding: 1rem 2rem;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                }
                .header h1 { font-size: 1.5rem; }
                .header .info { font-size: 0.9rem; opacity: 0.9; }
                .nav {
                    background: white;
                    padding: 0;
                    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
                    overflow-x: auto;
                }
                .nav ul {
                    list-style: none;
                    display: flex;
                    margin: 0;
                    padding: 0;
                }
                .nav li {
                    margin: 0;
                }
                .nav a {
                    display: block;
                    padding: 1rem 1.5rem;
                    text-decoration: none;
                    color: #666;
                    border-bottom: 3px solid transparent;
                    transition: all 0.3s;
                    white-space: nowrap;
                }
                .nav a:hover, .nav a.active {
                    color: #667eea;
                    border-bottom-color: #667eea;
                    background: #f8f9ff;
                }
                .container {
                    max-width: 1200px;
                    margin: 2rem auto;
                    padding: 0 1rem;
                }
                .card {
                    background: white;
                    border-radius: 10px;
                    padding: 2rem;
                    margin-bottom: 2rem;
                    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
                }
                .card h2 {
                    color: #333;
                    margin-bottom: 1.5rem;
                    display: flex;
                    align-items: center;
                    gap: 0.5rem;
                }
                .btn {
                    display: inline-block;
                    padding: 0.75rem 1.5rem;
                    margin: 0.25rem;
                    background: #667eea;
                    color: white;
                    text-decoration: none;
                    border-radius: 6px;
                    border: none;
                    cursor: pointer;
                    font-size: 0.9rem;
                    transition: all 0.3s;
                }
                .btn:hover { background: #5a6fd8; transform: translateY(-1px); }
                .btn.success { background: #00b894; }
                .btn.success:hover { background: #00a085; }
                .btn.danger { background: #e74c3c; }
                .btn.danger:hover { background: #c0392b; }
                .btn.warning { background: #f39c12; }
                .btn.warning:hover { background: #e67e22; }
                .alert {
                    padding: 1rem;
                    border-radius: 6px;
                    margin-bottom: 1rem;
                }
                .alert.success { background: #d4edda; color: #155724; border-left: 4px solid #28a745; }
                .alert.error { background: #f8d7da; color: #721c24; border-left: 4px solid #dc3545; }
                .alert.warning { background: #fff3cd; color: #856404; border-left: 4px solid #ffc107; }
                .alert.info { background: #d1ecf1; color: #0c5460; border-left: 4px solid #17a2b8; }
                .grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
                    gap: 1.5rem;
                }
                .status-indicator {
                    display: inline-flex;
                    align-items: center;
                    gap: 0.5rem;
                    padding: 0.5rem 1rem;
                    border-radius: 20px;
                    font-size: 0.85rem;
                    font-weight: 600;
                }
                .status-indicator.active { background: #d4edda; color: #155724; }
                .status-indicator.inactive { background: #f8d7da; color: #721c24; }
                .status-indicator.warning { background: #fff3cd; color: #856404; }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 1rem;
                }
                th, td {
                    text-align: left;
                    padding: 0.75rem;
                    border-bottom: 1px solid #eee;
                }
                th { background: #f8f9fa; font-weight: 600; }
                .form-group {
                    margin-bottom: 1.5rem;
                }
                .form-group label {
                    display: block;
                    margin-bottom: 0.5rem;
                    font-weight: 600;
                    color: #555;
                }
                .form-control {
                    width: 100%;
                    padding: 0.75rem;
                    border: 2px solid #e1e8ed;
                    border-radius: 6px;
                    font-size: 1rem;
                }
                .form-control:focus {
                    outline: none;
                    border-color: #667eea;
                }
                select.form-control {
                    cursor: pointer;
                }
                .progress-bar {
                    background: #e9ecef;
                    border-radius: 10px;
                    overflow: hidden;
                    height: 20px;
                }
                .progress-fill {
                    height: 100%;
                    background: linear-gradient(90deg, #667eea, #764ba2);
                    transition: width 0.3s;
                }
                @media (max-width: 768px) {
                    .header { padding: 1rem; flex-direction: column; gap: 0.5rem; }
                    .container { padding: 0 0.5rem; margin: 1rem auto; }
                    .nav ul { flex-wrap: wrap; }
                    .nav a { padding: 0.75rem 1rem; }
                }
            </style>
        </head>
        <body>
            <header class="header">
                <div>
                    <h1>🛡️ <?= $this->config['app_name'] ?></h1>
                    <div class="info">v<?= $this->config['version'] ?> | <?= defined('_PS_VERSION_') ? 'PrestaShop ' . _PS_VERSION_ : 'PrestaShop' ?></div>
                </div>
                <div>
                    <a href="?action=logout" class="btn btn-sm">Cerrar Sesión</a>
                </div>
            </header>
            
            <nav class="nav">
                <ul>
                    <li><a href="?action=dashboard" class="<?= ($_GET['action'] ?? 'dashboard') === 'dashboard' ? 'active' : '' ?>">📊 Dashboard</a></li>
                    <li><a href="?action=admins" class="<?= ($_GET['action'] ?? '') === 'admins' ? 'active' : '' ?>">👥 Administradores</a></li>
                    <li><a href="?action=cache" class="<?= ($_GET['action'] ?? '') === 'cache' ? 'active' : '' ?>">🗑️ Cache</a></li>
                    <li><a href="?action=debug" class="<?= ($_GET['action'] ?? '') === 'debug' ? 'active' : '' ?>">🐛 Debug</a></li>
                    <li><a href="?action=maintenance" class="<?= ($_GET['action'] ?? '') === 'maintenance' ? 'active' : '' ?>">🔧 Mantenimiento</a></li>
                    <li><a href="?action=permissions" class="<?= ($_GET['action'] ?? '') === 'permissions' ? 'active' : '' ?>">📁 Permisos</a></li>
                    <li><a href="?action=admin_folder" class="<?= ($_GET['action'] ?? '') === 'admin_folder' ? 'active' : '' ?>">📂 Carpeta Admin</a></li>
                    <li><a href="?action=disk_usage" class="<?= ($_GET['action'] ?? '') === 'disk_usage' ? 'active' : '' ?>">💾 Espacio</a></li>
                    <li><a href="?action=system_info" class="<?= ($_GET['action'] ?? '') === 'system_info' ? 'active' : '' ?>">ℹ️ Sistema</a></li>
                </ul>
            </nav>
            
            <div class="container">
        <?php
    }
    
    private function showDashboard() {
        if ($_GET['delete_file'] ?? false) {
            $this->deleteThisFile();
        }
        
        $system_status = $this->getSystemStatus();
        ?>
        <div class="card">
            <h2>📊 Estado del Sistema</h2>
            <div class="grid">
                <div>
                    <h3>Estado General</h3>
                    <p><strong>Mantenimiento:</strong> 
                        <span class="status-indicator <?= $system_status['maintenance'] ? 'active' : 'inactive' ?>">
                            <?= $system_status['maintenance'] ? '🔧 Activo' : '✅ Desactivo' ?>
                        </span>
                    </p>
                    <p><strong>Modo Debug:</strong> 
                        <span class="status-indicator <?= $system_status['debug'] ? 'warning' : 'inactive' ?>">
                            <?= $system_status['debug'] ? '🐛 Activo' : '✅ Desactivo' ?>
                        </span>
                    </p>
                    <p><strong>Admins registrados:</strong> <?= $system_status['admin_count'] ?></p>
                </div>
                <div>
                    <h3>Rendimiento</h3>
                    <p><strong>PHP:</strong> <?= PHP_VERSION ?></p>
                    <p><strong>Memoria:</strong> <?= ini_get('memory_limit') ?></p>
                    <p><strong>Max execution:</strong> <?= ini_get('max_execution_time') ?>s</p>
                    <p><strong>Carpeta admin:</strong> <?= $this->getAdminFolder() ?: 'No detectada' ?></p>
                </div>
            </div>
        </div>
        
        <div class="card">
            <h2>🚀 Acciones Rápidas</h2>
            <div style="display: flex; flex-wrap: wrap; gap: 1rem;">
                <a href="?action=cache&clear=all" class="btn success">🗑️ Limpiar Cache</a>
                <a href="?action=debug&toggle=1" class="btn warning">🐛 Toggle Debug</a>
                <a href="?action=maintenance&toggle=1" class="btn">🔧 Toggle Mantenimiento</a>
                <a href="?action=permissions&fix=auto" class="btn">📁 Reparar Permisos</a>
                <a href="?action=dashboard&delete_file=1" class="btn danger" onclick="return confirm('¿Estás seguro de eliminar este archivo? Esta acción no se puede deshacer.')">🗑️ Eliminar Este Archivo</a>
            </div>
        </div>
        
        <div class="alert warning">
            <strong>⚠️ Recordatorio de Seguridad:</strong> Esta herramienta otorga acceso completo al sistema. 
            Elimínala inmediatamente después de resolver el problema.
        </div>
        <?php
    }
    
    private function manageAdmins() {
        if ($_POST['action_type'] ?? false) {
            if ($_POST['action_type'] === 'change_password') {
                $this->changeAdminPassword($_POST['admin_id'], $_POST['new_password']);
            }
        }
        
        $admins = $this->getAdminUsers();
        
        ?>
        <div class="card">
            <h2>👥 Gestión de Administradores</h2>
            
            <?php if (empty($admins)): ?>
                <div class="alert error">No se encontraron administradores</div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Activo</th>
                            <th>Último acceso</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($admins as $admin): ?>
                        <tr>
                            <td><?= $admin['id_employee'] ?></td>
                            <td><?= htmlspecialchars($admin['firstname'] . ' ' . $admin['lastname']) ?></td>
                            <td><?= htmlspecialchars($admin['email']) ?></td>
                            <td>
                                <span class="status-indicator <?= $admin['active'] ? 'active' : 'inactive' ?>">
                                    <?= $admin['active'] ? 'Activo' : 'Inactivo' ?>
                                </span>
                            </td>
                            <td><?= $admin['last_connection_date'] ? $this->formatSpanishDate(strtotime($admin['last_connection_date'])) : 'Nunca' ?></td>
                            <td>
                                <button onclick="changePassword(<?= $admin['id_employee'] ?>, '<?= htmlspecialchars($admin['email']) ?>')" 
                                        class="btn btn-sm">🔑 Cambiar Password</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        
        <!-- Modal para cambio de contraseña -->
        <div id="passwordModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000;">
            <div style="position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); background:white; padding:2rem; border-radius:10px; min-width:400px;">
                <h3 id="modalTitle">Cambiar Contraseña</h3>
                <form method="post">
                    <input type="hidden" name="action_type" value="change_password">
                    <input type="hidden" name="admin_id" id="modalAdminId">
                    <div class="form-group">
                        <label>Nueva Contraseña:</label>
                        <input type="password" name="new_password" class="form-control" required minlength="8">
                        <small>Mínimo 8 caracteres</small>
                    </div>
                    <div style="text-align:right; margin-top:1rem;">
                        <button type="button" onclick="closeModal()" class="btn">Cancelar</button>
                        <button type="submit" class="btn success">Cambiar</button>
                    </div>
                </form>
            </div>
        </div>
        
        <script>
        function changePassword(adminId, email) {
            document.getElementById('modalAdminId').value = adminId;
            document.getElementById('modalTitle').textContent = 'Cambiar Password: ' + email;
            document.getElementById('passwordModal').style.display = 'block';
        }
        function closeModal() {
            document.getElementById('passwordModal').style.display = 'none';
        }
        </script>
        <?php
    }
    
    private function manageCache() {
        if ($_GET['clear'] ?? false) {
            $result = $this->clearCache($_GET['clear']);
            echo '<div class="alert ' . ($result['success'] ? 'success' : 'error') . '">' . $result['message'] . '</div>';
        }
        
        $cache_info = $this->getCacheInfo();
        ?>
        <div class="card">
            <h2>🗑️ Gestión de Cache</h2>
            
            <div class="grid">
                <div>
                    <h3>Estado de Cache</h3>
                    <p><strong>Smarty Cache:</strong> <?= $cache_info['smarty_files'] ?> archivos</p>
                    <p><strong>Cache FS:</strong> <?= $cache_info['cachefs_files'] ?> archivos</p>
                    <p><strong>Var Cache:</strong> <?= $cache_info['var_files'] ?> archivos</p>
                    <p><strong>Tamaño total:</strong> <?= $this->formatBytes($cache_info['total_size']) ?></p>
                </div>
                <div>
                    <h3>Acciones</h3>
                    <a href="?action=cache&clear=smarty" class="btn">🧹 Limpiar Smarty</a>
                    <a href="?action=cache&clear=cachefs" class="btn">🧹 Limpiar CacheFS</a>
                    <a href="?action=cache&clear=var" class="btn">🧹 Limpiar Var</a>
                    <a href="?action=cache&clear=all" class="btn danger">🗑️ Limpiar Todo</a>
                </div>
            </div>
        </div>
        <?php
    }
    
    private function manageDebug() {
        if ($_GET['toggle'] ?? false) {
            $this->toggleDebugMode();
        }
        if ($_POST['debug_action'] ?? false) {
            $this->handleDebugAction($_POST['debug_action'], $_POST);
        }
        
        $debug_status = $this->getDebugStatus();
        ?>
        <div class="card">
            <h2>🐛 Configuración de Debug</h2>
            
            <form method="post">
                <div class="grid">
                    <div class="form-group">
                        <label>Modo Desarrollo:</label>
                        <select name="debug_mode" class="form-control">
                            <option value="0" <?= !$debug_status['dev_mode'] ? 'selected' : '' ?>>Desactivado</option>
                            <option value="1" <?= $debug_status['dev_mode'] ? 'selected' : '' ?>>Activado</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Profiler:</label>
                        <select name="profiler" class="form-control">
                            <option value="0" <?= !$debug_status['profiler'] ? 'selected' : '' ?>>Desactivado</option>
                            <option value="1" <?= $debug_status['profiler'] ? 'selected' : '' ?>>Activado</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="disable_overrides" <?= $debug_status['disable_overrides'] ? 'checked' : '' ?>>
                        Desactivar todos los overrides
                    </label>
                </div>
                
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="force_compile" <?= $debug_status['force_compile'] ? 'checked' : '' ?>>
                        Forzar compilación de templates
                    </label>
                </div>
                
                <input type="hidden" name="debug_action" value="update_settings">
                <button type="submit" class="btn success">💾 Guardar Configuración</button>
            </form>
        </div>
        <?php
    }
    
    private function manageMaintenance() {
        if ($_GET['toggle'] ?? false) {
            $this->toggleMaintenanceMode();
        }
        
        $maintenance_status = $this->getMaintenanceStatus();
        ?>
        <div class="card">
            <h2>🔧 Modo Mantenimiento</h2>
            
            <div class="grid">
                <div>
                    <h3>Estado Actual</h3>
                    <p><strong>Mantenimiento:</strong> 
                        <span class="status-indicator <?= $maintenance_status['enabled'] ? 'active' : 'inactive' ?>">
                            <?= $maintenance_status['enabled'] ? '🔧 Activo' : '✅ Desactivo' ?>
                        </span>
                    </p>
                    <p><strong>IP Permitida:</strong> <?= $maintenance_status['ip'] ?: 'Ninguna' ?></p>
                </div>
                <div>
                    <h3>Control</h3>
                    <?php if ($maintenance_status['enabled']): ?>
                        <a href="?action=maintenance&toggle=1" class="btn success">✅ Desactivar Mantenimiento</a>
                    <?php else: ?>
                        <a href="?action=maintenance&toggle=1" class="btn warning">🔧 Activar Mantenimiento</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php
    }
    
    private function getSystemStatus() {
        return [
            'maintenance' => !Configuration::get('PS_SHOP_ENABLE'),
            'debug' => $this->isDebugMode(),
            'admin_count' => count($this->getAdminUsers())
        ];
    }
    
    private function getAdminUsers() {
        $sql = "SELECT * FROM " . _DB_PREFIX_ . "employee ORDER BY id_employee";
        return Db::getInstance()->executeS($sql) ?: [];
    }
    
    private function changeAdminPassword($admin_id, $new_password) {
        $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
        $sql = "UPDATE " . _DB_PREFIX_ . "employee SET passwd = '" . pSQL($password_hash) . "' WHERE id_employee = " . (int)$admin_id;
        
        if (Db::getInstance()->execute($sql)) {
            echo '<div class="alert success">✅ Contraseña cambiada correctamente</div>';
        } else {
            echo '<div class="alert error">❌ Error al cambiar la contraseña</div>';
        }
    }
    
    private function isDebugMode() {
        $config_file = './config/defines.inc.php';
        if (!file_exists($config_file)) return false;
        
        $content = file_get_contents($config_file);
        return strpos($content, "define('_PS_MODE_DEV_', true)") !== false;
    }
    
    private function getCacheInfo() {
        $cache_dirs = [
            'smarty' => './cache/smarty/',
            'cachefs' => './cache/cachefs/',
            'var' => './var/cache/'
        ];
        
        $info = ['total_size' => 0, 'smarty_files' => 0, 'cachefs_files' => 0, 'var_files' => 0];
        
        foreach ($cache_dirs as $type => $dir) {
            if (is_dir($dir)) {
                $files = $this->getDirInfo($dir);
                $info[$type . '_files'] = $files['count'];
                $info['total_size'] += $files['size'];
            }
        }
        
        return $info;
    }
    
    private function clearCache($type = 'all') {
        $cleared = 0;
        $dirs = [];
        
        switch ($type) {
            case 'smarty':
                $dirs = ['./cache/smarty/cache/', './cache/smarty/compile/'];
                break;
            case 'cachefs':
                $dirs = ['./cache/cachefs/'];
                break;
            case 'var':
                $dirs = ['./var/cache/'];
                break;
            case 'all':
                $dirs = ['./cache/smarty/cache/', './cache/smarty/compile/', './cache/cachefs/', './var/cache/'];
                break;
        }
        
        foreach ($dirs as $dir) {
            if (is_dir($dir)) {
                $cleared += $this->removeDirectoryContents($dir);
            }
        }
        
        // Limpiar cache de BD
        try {
            Db::getInstance()->execute("DELETE FROM " . _DB_PREFIX_ . "smarty_cache");
            Db::getInstance()->execute("DELETE FROM " . _DB_PREFIX_ . "smarty_lazy_cache");
            return ['success' => true, 'message' => "✅ Cache limpiado. $cleared archivos eliminados."];
        } catch (Exception $e) {
            return ['success' => false, 'message' => "⚠️ Cache parcialmente limpiado. Error en BD: " . $e->getMessage()];
        }
    }
    
    private function getDebugStatus() {
        $config_file = './config/defines.inc.php';
        $content = file_exists($config_file) ? file_get_contents($config_file) : '';
        
        return [
            'dev_mode' => strpos($content, "define('_PS_MODE_DEV_', true)") !== false,
            'profiler' => defined('_PS_DEBUG_PROFILING_') && _PS_DEBUG_PROFILING_,
            'disable_overrides' => strpos($content, "define('_PS_DISABLE_OVERRIDES_', true)") !== false,
            'force_compile' => defined('_PS_SMARTY_FORCE_COMPILE_') && _PS_SMARTY_FORCE_COMPILE_
        ];
    }
    
    private function handleDebugAction($action, $data) {
        if ($action === 'update_settings') {
            $this->updateDebugSettings($data);
        }
    }
    
    private function updateDebugSettings($data) {
        $config_file = './config/defines.inc.php';
        if (!file_exists($config_file)) {
            echo '<div class="alert error">❌ No se encuentra defines.inc.php</div>';
            return;
        }
        
        $content = file_get_contents($config_file);
        
        // Modo desarrollo
        $dev_mode = isset($data['debug_mode']) && $data['debug_mode'] == '1';
        $content = preg_replace(
            "/define\('_PS_MODE_DEV_',\s*(true|false)\)/",
            "define('_PS_MODE_DEV_', " . ($dev_mode ? 'true' : 'false') . ")",
            $content
        );
        
        // Overrides
        $disable_overrides = isset($data['disable_overrides']);
        if ($disable_overrides) {
            if (strpos($content, "_PS_DISABLE_OVERRIDES_") === false) {
                $content = str_replace(
                    "define('_PS_MODE_DEV_',",
                    "define('_PS_DISABLE_OVERRIDES_', true);\ndefine('_PS_MODE_DEV_',",
                    $content
                );
            } else {
                $content = preg_replace(
                    "/define\('_PS_DISABLE_OVERRIDES_',\s*(true|false)\)/",
                    "define('_PS_DISABLE_OVERRIDES_', true)",
                    $content
                );
            }
        } else {
            $content = preg_replace(
                "/define\('_PS_DISABLE_OVERRIDES_',\s*true\);\n?/",
                "",
                $content
            );
        }
        
        if (file_put_contents($config_file, $content)) {
            echo '<div class="alert success">✅ Configuración de debug actualizada</div>';
        } else {
            echo '<div class="alert error">❌ Error al actualizar la configuración</div>';
        }
    }
    
    private function toggleDebugMode() {
        $config_file = './config/defines.inc.php';
        if (!file_exists($config_file)) return;
        
        $content = file_get_contents($config_file);
        $debug_enabled = strpos($content, "define('_PS_MODE_DEV_', true)") !== false;
        
        if ($debug_enabled) {
            $content = str_replace("define('_PS_MODE_DEV_', true)", "define('_PS_MODE_DEV_', false)", $content);
            $status = 'desactivado';
        } else {
            $content = str_replace("define('_PS_MODE_DEV_', false)", "define('_PS_MODE_DEV_', true)", $content);
            $status = 'activado';
        }
        
        file_put_contents($config_file, $content);
        echo '<div class="alert success">✅ Modo debug ' . $status . '</div>';
    }
    
    private function getMaintenanceStatus() {
        return [
            'enabled' => !Configuration::get('PS_SHOP_ENABLE'),
            'ip' => Configuration::get('PS_MAINTENANCE_IP')
        ];
    }
    
    private function toggleMaintenanceMode() {
        $current = Configuration::get('PS_SHOP_ENABLE');
        $new_status = $current ? 0 : 1;
        
        Configuration::updateValue('PS_SHOP_ENABLE', $new_status);
        $status = $new_status ? 'desactivado' : 'activado';
        echo '<div class="alert success">✅ Modo mantenimiento ' . $status . '</div>';
    }
    
    private function managePermissions() {
        if ($_GET['fix'] ?? false) {
            $this->fixPermissions();
        }
        
        $permissions = $this->checkPermissions();
        ?>
        <div class="card">
            <h2>📁 Gestión de Permisos</h2>
            
            <div style="margin-bottom: 1rem;">
                <a href="?action=permissions&fix=auto" class="btn success">🔧 Reparar Permisos Automáticamente</a>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>Directorio</th>
                        <th>Actual</th>
                        <th>Recomendado</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($permissions as $dir => $info): ?>
                    <tr>
                        <td><?= $dir ?></td>
                        <td><?= $info['current'] ?></td>
                        <td><?= $info['recommended'] ?></td>
                        <td>
                            <span class="status-indicator <?= $info['ok'] ? 'active' : 'warning' ?>">
                                <?= $info['ok'] ? '✅ OK' : '⚠️ Revisar' ?>
                            </span>
                        </td>
                        <td>
                            <?php if (!$info['ok']): ?>
                                <a href="?action=permissions&fix_dir=<?= urlencode($dir) ?>" class="btn btn-sm">🔧 Reparar</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
    }
    
    private function checkPermissions() {
        $dirs = [
            './cache/' => '755',
            './config/' => '755',
            './download/' => '755',
            './img/' => '755',
            './log/' => '755',
            './mails/' => '755',
            './modules/' => '755',
            './themes/' => '755',
            './translations/' => '755',
            './upload/' => '755',
            './var/' => '755'
        ];
        
        $result = [];
        foreach ($dirs as $dir => $recommended) {
            if (is_dir($dir)) {
                $current = substr(sprintf('%o', fileperms($dir)), -3);
                $result[$dir] = [
                    'current' => $current,
                    'recommended' => $recommended,
                    'ok' => $current === $recommended
                ];
            }
        }
        
        return $result;
    }
    
    private function fixPermissions() {
        $dirs = [
            './cache/' => 0755,
            './config/' => 0755,
            './download/' => 0755,
            './img/' => 0755,
            './log/' => 0755,
            './mails/' => 0755,
            './modules/' => 0755,
            './themes/' => 0755,
            './translations/' => 0755,
            './upload/' => 0755,
            './var/' => 0755
        ];
        
        $fixed = 0;
        foreach ($dirs as $dir => $perm) {
            if (is_dir($dir) && chmod($dir, $perm)) {
                $fixed++;
            }
        }
        
        echo '<div class="alert success">✅ Permisos reparados: ' . $fixed . ' directorios</div>';
    }
    
    private function manageAdminFolder() {
        if ($_POST['action_type'] ?? false) {
            if ($_POST['action_type'] === 'rename_admin') {
                $this->renameAdminFolder($_POST['new_name']);
            }
        }
        
        $admin_folder = $this->getAdminFolder();
        ?>
        <div class="card">
            <h2>📂 Gestión de Carpeta Admin</h2>
            
            <div class="grid">
                <div>
                    <h3>Estado Actual</h3>
                    <p><strong>Carpeta detectada:</strong> <?= $admin_folder ?: 'No encontrada' ?></p>
                    <p><strong>Ruta completa:</strong> <?= $admin_folder ? realpath('./' . $admin_folder) : 'N/A' ?></p>
                    <?php if ($admin_folder): ?>
                        <p><strong>Acceso:</strong> <a href="<?= $admin_folder ?>" target="_blank" class="btn btn-sm">🔗 Abrir Admin</a></p>
                    <?php endif; ?>
                </div>
                <div>
                    <h3>Cambiar Nombre</h3>
                    <form method="post">
                        <input type="hidden" name="action_type" value="rename_admin">
                        <div class="form-group">
                            <label>Nuevo nombre:</label>
                            <input type="text" name="new_name" class="form-control" placeholder="admin_nuevo_nombre" pattern="[a-zA-Z0-9_-]+" required>
                            <small>Solo letras, números, guiones y guiones bajos</small>
                        </div>
                        <button type="submit" class="btn warning">📝 Renombrar Carpeta</button>
                    </form>
                </div>
            </div>
        </div>
        <?php
    }
    
    private function getAdminFolder() {
        // Método 1: Usar constante _PS_ADMIN_DIR_ si está disponible
        if (defined('_PS_ADMIN_DIR_')) {
            return basename(_PS_ADMIN_DIR_);
        }
        
        // Método 2: Buscar por archivos específicos del admin
        $admin_files = ['get-file-admin.php', 'ajax-tab.php', 'header.inc.php'];
        $dirs = glob('./*', GLOB_ONLYDIR);
        
        foreach ($dirs as $dir) {
            $dir_name = basename($dir);
            
            // Saltar directorios obvios que no son admin
            if (in_array($dir_name, ['cache', 'classes', 'config', 'controllers', 'img', 'js', 'css', 'modules', 'themes', 'tools', 'upload', 'download', 'var', 'vendor', 'install'])) {
                continue;
            }
            
            // Buscar archivos específicos del admin
            $admin_file_found = false;
            foreach ($admin_files as $file) {
                if (file_exists($dir . '/' . $file)) {
                    $admin_file_found = true;
                    break;
                }
            }
            
            // Verificar estructura típica del admin
            $has_admin_structure = (
                file_exists($dir . '/index.php') && 
                (file_exists($dir . '/controllers/') || file_exists($dir . '/tabs/')) &&
                (strpos($dir_name, 'admin') !== false || $admin_file_found)
            );
            
            if ($has_admin_structure || $admin_file_found) {
                return $dir_name;
            }
        }
        
        // Método 3: Buscar carpetas que empiecen con "admin" y tengan estructura válida
        foreach ($dirs as $dir) {
            $dir_name = basename($dir);
            if (preg_match('/^admin[0-9a-zA-Z]*$/', $dir_name) && 
                file_exists($dir . '/index.php')) {
                return $dir_name;
            }
        }
        
        return null;
    }
    
    private function renameAdminFolder($new_name) {
        $current_folder = $this->getAdminFolder();
        if (!$current_folder) {
            echo '<div class="alert error">❌ No se pudo detectar la carpeta admin actual</div>';
            return;
        }
        
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $new_name)) {
            echo '<div class="alert error">❌ Nombre inválido. Solo se permiten letras, números, guiones y guiones bajos</div>';
            return;
        }
        
        if (file_exists('./' . $new_name)) {
            echo '<div class="alert error">❌ Ya existe una carpeta con ese nombre</div>';
            return;
        }
        
        if (rename('./' . $current_folder, './' . $new_name)) {
            echo '<div class="alert success">✅ Carpeta admin renombrada de "' . $current_folder . '" a "' . $new_name . '"</div>';
        } else {
            echo '<div class="alert error">❌ Error al renombrar la carpeta</div>';
        }
    }
    
    private function showDiskUsage() {
        $usage = $this->getDiskUsage();
        ?>
        <div class="card">
            <h2>💾 Análisis de Espacio en Disco</h2>
            
            <div class="grid">
                <div>
                    <h3>Resumen General</h3>
                    <p><strong>Tamaño total:</strong> <?= $this->formatBytes($usage['total_size']) ?></p>
                    <p><strong>Número de archivos:</strong> <?= number_format($usage['total_files']) ?></p>
                </div>
                <div>
                    <h3>Top 5 Directorios</h3>
                    <?php foreach (array_slice($usage['directories'], 0, 5) as $dir => $size): ?>
                        <div style="margin-bottom: 0.5rem;">
                            <div style="display: flex; justify-content: space-between;">
                                <span><?= $dir ?></span>
                                <span><?= $this->formatBytes($size) ?></span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?= ($size / $usage['total_size']) * 100 ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <h3>Detalle por Directorio</h3>
            <table>
                <thead>
                    <tr>
                        <th>Directorio</th>
                        <th>Tamaño</th>
                        <th>% del Total</th>
                        <th>Archivos</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usage['directories'] as $dir => $size): ?>
                    <tr>
                        <td><?= $dir ?></td>
                        <td><?= $this->formatBytes($size) ?></td>
                        <td><?= number_format(($size / $usage['total_size']) * 100, 1) ?>%</td>
                        <td><?= number_format($usage['file_counts'][$dir] ?? 0) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
    }
    
    private function getDiskUsage() {
        $directories = [
            './cache/' => 0,
            './img/' => 0,
            './modules/' => 0,
            './themes/' => 0,
            './upload/' => 0,
            './download/' => 0,
            './log/' => 0,
            './var/' => 0,
            './config/' => 0,
            './controllers/' => 0,
            './classes/' => 0
        ];
        
        $file_counts = [];
        $total_size = 0;
        $total_files = 0;
        
        foreach ($directories as $dir => $size) {
            if (is_dir($dir)) {
                $info = $this->getDirInfo($dir);
                $directories[$dir] = $info['size'];
                $file_counts[$dir] = $info['count'];
                $total_size += $info['size'];
                $total_files += $info['count'];
            }
        }
        
        // Ordenar por tamaño
        arsort($directories);
        
        return [
            'total_size' => $total_size,
            'total_files' => $total_files,
            'directories' => $directories,
            'file_counts' => $file_counts
        ];
    }
    
    private function showSystemInfo() {
        $db_info = $this->getDatabaseInfo();
        ?>
        <div class="card">
            <h2>ℹ️ Información del Sistema</h2>
            
            <div class="grid">
                <div>
                    <h3>PrestaShop</h3>
                    <p><strong>Versión:</strong> <?= defined('_PS_VERSION_') ? _PS_VERSION_ : 'No detectada' ?></p>
                    <p><strong>Ruta:</strong> <?= _PS_ROOT_DIR_ ?></p>
                    <p><strong>URL Base:</strong> <?= Configuration::get('PS_SHOP_DOMAIN') ?></p>
                    <p><strong>SSL:</strong> <?= Configuration::get('PS_SSL_ENABLED') ? 'Habilitado' : 'Deshabilitado' ?></p>
                </div>
                
                <div>
                    <h3>PHP</h3>
                    <p><strong>Versión:</strong> <?= PHP_VERSION ?></p>
                    <p><strong>Memoria:</strong> <?= ini_get('memory_limit') ?></p>
                    <p><strong>Upload Max:</strong> <?= ini_get('upload_max_filesize') ?></p>
                    <p><strong>Max Execution:</strong> <?= ini_get('max_execution_time') ?>s</p>
                    <p><strong>Display Errors:</strong> <?= ini_get('display_errors') ? 'On' : 'Off' ?></p>
                </div>
                
                <div>
                    <h3>Base de Datos</h3>
                    <p><strong>Servidor:</strong> <?= _DB_SERVER_ ?></p>
                    <p><strong>Base de datos:</strong> <?= _DB_NAME_ ?></p>
                    <p><strong>Prefijo:</strong> <?= _DB_PREFIX_ ?></p>
                    <p><strong>Versión MySQL:</strong> <?= $db_info['version'] ?></p>
                    <p><strong>Tamaño BD:</strong> <?= $this->formatBytes($db_info['size']) ?></p>
                </div>
                
                <div>
                    <h3>Servidor</h3>
                    <p><strong>OS:</strong> <?= PHP_OS ?></p>
                    <p><strong>Server:</strong> <?= $_SERVER['SERVER_SOFTWARE'] ?? 'No detectado' ?></p>
                    <p><strong>Document Root:</strong> <?= $_SERVER['DOCUMENT_ROOT'] ?></p>
                    <p><strong>IP Servidor:</strong> <?= $_SERVER['SERVER_ADDR'] ?? 'No detectada' ?></p>
                </div>
            </div>
            
            <h3>Extensiones PHP Críticas</h3>
            <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                <?php
                $required_extensions = ['mysql', 'pdo', 'pdo_mysql', 'curl', 'gd', 'mbstring', 'zip', 'xml', 'json'];
                foreach ($required_extensions as $ext):
                    $loaded = extension_loaded($ext);
                ?>
                    <span class="status-indicator <?= $loaded ? 'active' : 'inactive' ?>">
                        <?= $ext ?> <?= $loaded ? '✅' : '❌' ?>
                    </span>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }
    
    private function getDatabaseInfo() {
        try {
            $version_result = Db::getInstance()->getRow("SELECT VERSION() as version");
            $size_result = Db::getInstance()->getRow("
                SELECT ROUND(SUM(data_length + index_length), 2) AS size 
                FROM information_schema.tables 
                WHERE table_schema = '" . _DB_NAME_ . "'
            ");
            
            return [
                'version' => $version_result['version'] ?? 'Desconocida',
                'size' => $size_result['size'] ?? 0
            ];
        } catch (Exception $e) {
            return ['version' => 'Error', 'size' => 0];
        }
    }
    
    private function getDirInfo($dir) {
        $size = 0;
        $count = 0;
        
        if (is_dir($dir)) {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS)
            );
            
            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $size += $file->getSize();
                    $count++;
                }
            }
        }
        
        return ['size' => $size, 'count' => $count];
    }
    
    private function removeDirectoryContents($dir) {
        $count = 0;
        if (is_dir($dir)) {
            $files = array_diff(scandir($dir), array('.', '..'));
            foreach ($files as $file) {
                $path = $dir . '/' . $file;
                if (is_dir($path)) {
                    $count += $this->removeDirectoryContents($path . '/');
                    rmdir($path);
                } else {
                    unlink($path);
                    $count++;
                }
            }
        }
        return $count;
    }
    
    private function formatBytes($bytes, $precision = 2) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
    
    private function deleteThisFile() {
        $filename = basename($_SERVER['PHP_SELF']);
        if (file_exists($filename)) {
            if (unlink($filename)) {
                echo '<div class="alert success">✅ Archivo eliminado correctamente. Redirigiendo...</div>';
                echo '<script>setTimeout(function(){ window.location.href = "./"; }, 2000);</script>';
            } else {
                echo '<div class="alert error">❌ Error al eliminar el archivo. Verifique los permisos.</div>';
            }
        } else {
            echo '<div class="alert error">❌ Archivo no encontrado.</div>';
        }
    }
    
    private function formatSpanishDate($timestamp = null) {
        if ($timestamp === null) {
            $timestamp = time();
        }
        
        // Configurar locale a español si está disponible
        $old_locale = setlocale(LC_TIME, 0);
        setlocale(LC_TIME, 'es_ES.UTF-8', 'es_ES', 'Spanish_Spain', 'Spanish');
        
        // Formato: día/mes/año hora:minuto:segundo
        $formatted = date('d/m/Y H:i:s', $timestamp);
        
        // Restaurar locale anterior
        setlocale(LC_TIME, $old_locale);
        
        return $formatted;
    }
    
    private function logout() {
        session_destroy();
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
    
    private function showFooter() {
        ?>
            </div>
            <footer style="text-align: center; padding: 2rem; color: #666; border-top: 1px solid #eee; margin-top: 2rem;">
                <p><?= $this->config['app_name'] ?> v<?= $this->config['version'] ?> | 
                <strong style="color: #e74c3c;">⚠️ Eliminar este archivo después del uso</strong></p>
            </footer>
        </body>
        </html>
        <?php
    }
}

// Inicializar la aplicación
$app = new PSEmergencyTool($CONFIG);
$app->run();
?>