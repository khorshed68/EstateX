<?php

// Check if OCI8 is loaded
$oci8Loaded = extension_loaded('oci8');
$pdoOciLoaded = extension_loaded('pdo_oci');

// Get system PATH
$path = getenv('PATH');
$paths = explode(PATH_SEPARATOR, $path);

// Check if Instant Client 23 is in PATH
$instantClientFound = false;
$foundPath = '';
foreach ($paths as $p) {
    if (stripos($p, 'instantclient') !== false) {
        $instantClientFound = true;
        $foundPath = $p;
        break;
    }
}

// Find php.ini path
$iniPath = php_ini_loaded_file();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EstateX - Oracle OCI8 Environment Diagnostic</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #0b0f19;
            color: #e2e8f0;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-6">
    <div class="w-full max-w-2xl bg-slate-900 border border-slate-800 rounded-3xl p-8 shadow-2xl">
        <div class="flex items-center gap-4 mb-6">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-500 flex items-center justify-center text-white text-xl">
                <i class="fa-solid fa-screwdriver-wrench"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-white">OCI8 Environment Diagnostics</h1>
                <p class="text-xs text-slate-400">Verifying PHP and Oracle Instant Client compatibility</p>
            </div>
        </div>

        <div class="space-y-6">
            <!-- Status List -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- OCI8 Status -->
                <div class="p-4 rounded-2xl border <?php echo $oci8Loaded ? 'bg-green-500/10 border-green-500/20' : 'bg-red-500/10 border-red-500/20'; ?>">
                    <span class="text-xs text-slate-400 font-semibold block uppercase">OCI8 Extension</span>
                    <div class="flex items-center gap-2 mt-2">
                        <i class="fa-solid <?php echo $oci8Loaded ? 'fa-circle-check text-green-400' : 'fa-circle-xmark text-red-400'; ?> text-lg"></i>
                        <span class="font-bold text-white"><?php echo $oci8Loaded ? 'Loaded successfully' : 'Not loaded'; ?></span>
                    </div>
                </div>

                <!-- PDO OCI Status -->
                <div class="p-4 rounded-2xl border <?php echo $pdoOciLoaded ? 'bg-green-500/10 border-green-500/20' : 'bg-red-500/10 border-red-500/20'; ?>">
                    <span class="text-xs text-slate-400 font-semibold block uppercase">PDO OCI Extension</span>
                    <div class="flex items-center gap-2 mt-2">
                        <i class="fa-solid <?php echo $pdoOciLoaded ? 'fa-circle-check text-green-400' : 'fa-circle-xmark text-red-400'; ?> text-lg"></i>
                        <span class="font-bold text-white"><?php echo $pdoOciLoaded ? 'Loaded successfully' : 'Not loaded'; ?></span>
                    </div>
                </div>
            </div>

            <!-- Path Check -->
            <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800">
                <span class="text-xs text-slate-400 font-semibold block uppercase">Oracle Instant Client in System PATH</span>
                <?php if ($instantClientFound): ?>
                    <div class="flex items-start gap-3 mt-2 text-green-400 text-sm">
                        <i class="fa-solid fa-check-double mt-0.5"></i>
                        <div>
                            <span class="font-bold text-slate-200">Found in path:</span>
                            <code class="block bg-slate-900 px-2 py-1 rounded text-xs text-slate-300 mt-1 font-mono break-all"><?php echo htmlspecialchars($foundPath); ?></code>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="flex items-start gap-3 mt-2 text-red-400 text-sm">
                        <i class="fa-solid fa-triangle-exclamation mt-0.5 animate-pulse"></i>
                        <div>
                            <span class="font-bold text-slate-200">Instant Client path is missing from getenv('PATH')!</span>
                            <p class="text-xs text-slate-500 mt-1">PHP cannot find the required Oracle DLL libraries (oci.dll) to load the extension.</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Configuration Info -->
            <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800 space-y-3">
                <div>
                    <span class="text-xs text-slate-400 font-semibold block uppercase">Loaded php.ini</span>
                    <code class="block text-xs text-slate-300 mt-1 font-mono break-all"><?php echo htmlspecialchars($iniPath); ?></code>
                </div>
                <div>
                    <span class="text-xs text-slate-400 font-semibold block uppercase">PHP Architecture</span>
                    <code class="block text-xs text-slate-300 mt-1 font-mono"><?php echo PHP_INT_SIZE === 8 ? '64-bit (x64)' : '32-bit (x86)'; ?></code>
                </div>
            </div>

            <!-- Fix Instruction Section -->
            <?php if (!$oci8Loaded): ?>
                <div class="p-5 rounded-2xl bg-blue-500/5 border border-blue-500/20 text-sm space-y-3">
                    <h3 class="font-bold text-blue-400 flex items-center gap-2">
                        <i class="fa-solid fa-circle-info"></i>
                        How to Solve this issue:
                    </h3>
                    <p class="text-xs text-slate-300 leading-relaxed">
                        The `php_oci8_19.dll` module requires Oracle Instant Client libraries in the running environment path. Because Windows processes cache their environmental paths, any active terminal, text editor (like VS Code), or background web server (like Apache or PHP Artisan) will **not** pick up new path changes until they are fully closed and restarted.
                    </p>
                    <ol class="list-decimal list-inside space-y-2 text-xs text-slate-400">
                        <li>Ensure you added <code class="font-mono text-slate-200">E:\instantclient-basic-windows.x64-23.26.2.0.0\instantclient_23_0</code> to your <strong>System Environmental variables</strong> (Path).</li>
                        <li><strong>Restart your IDE (VS Code, PHPStorm, etc.):</strong> Close all editor windows completely and reopen them.</li>
                        <li><strong>Restart XAMPP Control Panel:</strong> Right-click the XAMPP icon in your Windows Taskbar tray, select <strong>Quit</strong>, then launch XAMPP again and click Start on Apache.</li>
                        <li><strong>Re-run serve:</strong> Open a new terminal and run <code class="font-mono text-slate-200">php artisan serve</code>.</li>
                    </ol>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
