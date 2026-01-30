<?php
session_start();

if(!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$username = $_SESSION['username'];
$role = $_SESSION['role'] ?? 'Konsument';
$login_time = $_SESSION['login_time'] ?? date('Y-m-d H:i:s');

$role_hierarchy = ['Administrator' => 5, 'Superuser' => 4, 'Master' => 3, 'Slave' => 2, 'Konsument' => 1];
$current_role_level = $role_hierarchy[$role] ?? 0;
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>L.I.P - Desktop</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body.desktop-mode {
            display: flex;
            flex-direction: column;
            height: 100vh;
            overflow: hidden;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .navbar { position: sticky; top: 0; z-index: 1000; flex-shrink: 0; }
        .running-apps { display: flex; gap: 10px; padding: 10px 20px; border-top: 1px solid #e0e0e0; background: #f5f5f5; }
        .app-button { padding: 6px 12px; background: white; border: 1px solid #e0e0e0; border-radius: 4px; font-size: 12px; cursor: pointer; transition: all 0.2s; }
        .app-button:hover { background: #2c5aa0; color: white; }
        .app-button.active { background: #2c5aa0; color: white; }
        .desktop-container { flex: 1; overflow: auto; padding: 20px; }
        .desktop-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: 20px; }
        .desktop-icon { display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px; padding: 12px; border-radius: 8px; background: rgba(255,255,255,0.1); border: 2px solid rgba(255,255,255,0.2); aspect-ratio: 1; cursor: pointer; transition: all 0.2s; }
        .desktop-icon:hover { background: rgba(255,255,255,0.15); transform: scale(1.05); }
        .desktop-icon-image { font-size: 48px; }
        .desktop-icon-label { color: white; font-size: 11px; font-weight: 600; text-align: center; text-shadow: 0 1px 2px rgba(0,0,0,0.3); }
    </style>
</head>
<body class="desktop-mode" data-role="<?php echo $role; ?>" data-username="<?php echo $username; ?>">
    
    <nav class="navbar">
        <div class="navbar-content">
            <div class="navbar-left">
                <div class="navbar-logo">L.I.P</div>
                <span class="role-badge role-administrator"><?php echo $role; ?></span>
            </div>
            <div class="navbar-center">
                <div class="time-display" id="timeDisplay" title="Klicken für Menü">00:00:00.000</div>
                <div class="time-dropdown" id="timeDropdown">
                    <ul>
                        <li><a href="#" onclick="openApp('Editor'); return false;">📄 Editor</a></li>
                        <li><a href="#" onclick="openApp('Notizen'); return false;">📝 Notizen</a></li>
                        <li><a href="#" onclick="openApp('Taschenrechner'); return false;">🧮 Taschenrechner</a></li>
                        <li class="dropdown-divider"></li>
                        <li class="dropdown-submenu">
                            <a href="#">🔧 Verwalten</a>
                            <div class="submenu">
                                <?php if($current_role_level >= 2): ?>
                                    <a href="#" onclick="openApp('Konsumenten'); return false;">👥 Konsumenten</a>
                                <?php endif; ?>
                                <?php if($current_role_level >= 3): ?>
                                    <a href="#" onclick="openApp('Slaves'); return false;">🔗 Slaves</a>
                                <?php endif; ?>
                                <?php if($current_role_level >= 4): ?>
                                    <a href="#" onclick="openApp('Benutzer'); return false;">👔 Benutzer</a>
                                <?php endif; ?>
                                <?php if($current_role_level >= 5): ?>
                                    <a href="#" onclick="openApp('System'); return false;">⚙️ System</a>
                                <?php endif; ?>
                            </div>
                        </li>
                        <li class="dropdown-divider"></li>
                        <li><a href="#" onclick="openApp('Einstellungen'); return false;">⚙️ Einstellungen</a></li>
                    </ul>
                </div>
            </div>
            <div class="navbar-right">
                <span class="user-name"><?php echo $username; ?></span>
                <button class="user-avatar" id="userBtn"><?php echo strtoupper($username[0]); ?></button>
                <div class="user-dropdown" id="userDropdown">
                    <a href="#" onclick="openApp('Profil'); return false;">👤 Profil</a>
                    <a href="logout.php" class="logout-link">🚪 Logout</a>
                </div>
            </div>
        </div>
        <div class="running-apps">
            <div class="app-button active">🖥️ Desktop</div>
            <div class="app-button" onclick="location.href='dashboard.php'">📊 Dashboard</div>
        </div>
    </nav>
    
    <div class="desktop-container">
        <div class="desktop-grid" id="grid"></div>
    </div>
    
    <div class="modal" id="appModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">App</h2>
                <button class="modal-close" id="closeBtn">&times;</button>
            </div>
            <div class="modal-body" id="modalBody"></div>
        </div>
    </div>

    <script>
        // ===== GLOBALE VARIABLEN =====
        let sessionNotes = [];
        let calcDisplay = '0';

        // ===== UHRZEIT =====
        function updateTime() {
            const now = new Date();
            const h = String(now.getHours()).padStart(2, '0');
            const m = String(now.getMinutes()).padStart(2, '0');
            const s = String(now.getSeconds()).padStart(2, '0');
            const ms = String(now.getMilliseconds()).padStart(3, '0');
            document.getElementById('timeDisplay').textContent = h + ':' + m + ':' + s + '.' + ms;
        }
        updateTime();
        setInterval(updateTime, 50);

        // ===== DROPDOWNS =====
        document.getElementById('timeDisplay').onclick = function(e) {
            e.stopPropagation();
            closeDropdowns();
            document.getElementById('timeDropdown').classList.add('active');
        };
        
        document.getElementById('userBtn').onclick = function(e) {
            e.stopPropagation();
            closeDropdowns();
            document.getElementById('userDropdown').classList.add('active');
        };
        
        document.onclick = function() {
            closeDropdowns();
        };

        function closeDropdowns() {
            document.getElementById('timeDropdown').classList.remove('active');
            document.getElementById('userDropdown').classList.remove('active');
        }

        // ===== DESKTOP ICONS =====
        const icons = [
            {icon: '📄', label: 'Editor'},
            {icon: '📝', label: 'Notizen'},
            {icon: '🧮', label: 'Rechner'},
            {icon: '⚙️', label: 'Einstellungen'},
            {icon: '👤', label: 'Profil'}
        ];

        const grid = document.getElementById('grid');
        icons.forEach(icon => {
            const div = document.createElement('div');
            div.className = 'desktop-icon';
            div.onclick = function() { openApp(icon.label); };
            div.innerHTML = '<div class="desktop-icon-image">' + icon.icon + '</div><div class="desktop-icon-label">' + icon.label + '</div>';
            grid.appendChild(div);
        });

        // ===== MODAL =====
        const modal = document.getElementById('appModal');
        document.getElementById('closeBtn').onclick = function() { modal.classList.remove('active'); };
        modal.onclick = function(e) { if(e.target === modal) modal.classList.remove('active'); };
        document.onkeydown = function(e) { if(e.key === 'Escape') modal.classList.remove('active'); };

        // ===== EDITOR APP =====
        function showEditor() {
            const body = document.getElementById('modalBody');
            body.innerHTML = `
                <div style="padding: 20px;">
                    <textarea id="editorText" placeholder="Schreiben Sie Ihren Text hier..." style="width: 100%; height: 250px; padding: 10px; border: 1px solid #e0e0e0; border-radius: 4px; font-family: monospace; font-size: 13px; resize: vertical;"></textarea>
                    <div style="margin-top: 15px; display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px;">
                        <button onclick="downloadFile('txt')" style="padding: 8px; background: #2c5aa0; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 600;">📄 TXT</button>
                        <button onclick="downloadFile('html')" style="padding: 8px; background: #2c5aa0; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 600;">🌐 HTML</button>
                        <button onclick="downloadFile('json')" style="padding: 8px; background: #2c5aa0; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 600;">📊 JSON</button>
                        <button onclick="clearText()" style="padding: 8px; background: #d32f2f; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 600;">🗑️ Leeren</button>
                    </div>
                </div>
            `;
        }

        function downloadFile(format) {
            const text = document.getElementById('editorText').value;
            if(!text.trim()) {
                alert('Bitte geben Sie zuerst Text ein!');
                return;
            }

            let content = text;
            let type = 'text/plain';
            let ext = format;

            if(format === 'html') {
                content = `<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Dokument</title></head><body><pre>${text.replace(/</g, '&lt;').replace(/>/g, '&gt;')}</pre></body></html>`;
                type = 'text/html';
            } else if(format === 'json') {
                content = JSON.stringify({content: text, created: new Date().toISOString()}, null, 2);
                type = 'application/json';
            }

            const blob = new Blob([content], {type: type});
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'dokument.' + ext;
            a.click();
            URL.revokeObjectURL(url);
        }

        function clearText() {
            if(confirm('Text wirklich löschen?')) {
                document.getElementById('editorText').value = '';
            }
        }

        // ===== NOTIZEN APP =====
        function showNotes() {
            const body = document.getElementById('modalBody');
            body.innerHTML = `
                <div style="padding: 20px;">
                    <textarea id="newNote" placeholder="Neue Notiz..." style="width: 100%; height: 100px; padding: 10px; border: 1px solid #e0e0e0; border-radius: 4px; font-size: 13px;"></textarea>
                    <button onclick="addNote()" style="width: 100%; padding: 10px; margin-top: 10px; background: #2c5aa0; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 600;">➕ Speichern</button>
                    <div id="notesList" style="margin-top: 20px;"></div>
                </div>
            `;
            loadNotes();
        }

        function addNote() {
            const text = document.getElementById('newNote').value.trim();
            if(!text) {
                alert('Bitte geben Sie einen Text ein!');
                return;
            }
            sessionNotes.push({
                id: Date.now(),
                text: text,
                date: new Date().toLocaleDateString('de-DE'),
                time: new Date().toLocaleTimeString('de-DE')
            });
            document.getElementById('newNote').value = '';
            loadNotes();
        }

        function loadNotes() {
            const list = document.getElementById('notesList');
            if(sessionNotes.length === 0) {
                list.innerHTML = '<p style="color: #666;">Keine Notizen vorhanden</p>';
                return;
            }
            list.innerHTML = sessionNotes.reverse().map(note => `
                <div style="padding: 10px; border: 1px solid #e0e0e0; margin-bottom: 10px; border-radius: 4px; background: #f5f5f5;">
                    <p style="margin: 0 0 5px 0;">${note.text}</p>
                    <small style="color: #666;">${note.date} ${note.time}</small>
                    <button onclick="deleteNote(${note.id})" style="float: right; padding: 3px 8px; background: #d32f2f; color: white; border: none; border-radius: 3px; cursor: pointer; font-size: 11px;">Löschen</button>
                </div>
            `).join('');
            sessionNotes.reverse();
        }

        function deleteNote(id) {
            sessionNotes = sessionNotes.filter(n => n.id !== id);
            loadNotes();
        }

        // ===== TASCHENRECHNER APP =====
        function showCalculator() {
            const body = document.getElementById('modalBody');
            body.innerHTML = `
                <div style="padding: 20px;">
                    <input type="text" id="calcDisplay" readonly value="0" style="width: 100%; padding: 15px; font-size: 24px; font-weight: 700; text-align: right; border: 1px solid #e0e0e0; border-radius: 4px; margin-bottom: 15px;">
                    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px;">
                        <button onclick="calcBtn('C')" style="padding: 15px; background: #f57c00; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 600;">C</button>
                        <button onclick="calcBtn('/')" style="padding: 15px; background: #00a8e8; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 600;">÷</button>
                        <button onclick="calcBtn('*')" style="padding: 15px; background: #00a8e8; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 600;">×</button>
                        <button onclick="calcBtn('Delete')" style="padding: 15px; background: #ff5722; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 600;">⌫</button>
                        
                        <button onclick="calcBtn('7')" style="padding: 15px; background: #2c5aa0; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 600;">7</button>
                        <button onclick="calcBtn('8')" style="padding: 15px; background: #2c5aa0; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 600;">8</button>
                        <button onclick="calcBtn('9')" style="padding: 15px; background: #2c5aa0; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 600;">9</button>
                        <button onclick="calcBtn('-')" style="padding: 15px; background: #00a8e8; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 600;">−</button>
                        
                        <button onclick="calcBtn('4')" style="padding: 15px; background: #2c5aa0; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 600;">4</button>
                        <button onclick="calcBtn('5')" style="padding: 15px; background: #2c5aa0; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 600;">5</button>
                        <button onclick="calcBtn('6')" style="padding: 15px; background: #2c5aa0; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 600;">6</button>
                        <button onclick="calcBtn('+')" style="padding: 15px; background: #00a8e8; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 600;">+</button>
                        
                        <button onclick="calcBtn('1')" style="padding: 15px; background: #2c5aa0; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 600;">1</button>
                        <button onclick="calcBtn('2')" style="padding: 15px; background: #2c5aa0; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 600;">2</button>
                        <button onclick="calcBtn('3')" style="padding: 15px; background: #2c5aa0; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 600;">3</button>
                        <button onclick="calcBtn('%')" style="padding: 15px; background: #00a8e8; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 600;">%</button>
                        
                        <button onclick="calcBtn('0')" style="padding: 15px; background: #2c5aa0; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 600; grid-column: span 2;">0</button>
                        <button onclick="calcBtn('.')" style="padding: 15px; background: #2c5aa0; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 600;">.</button>
                        <button onclick="calcBtn('=')" style="padding: 15px; background: #388e3c; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 600;">=</button>
                    </div>
                </div>
            `;
            calcDisplay = '0';
        }

        function calcBtn(val) {
            const display = document.getElementById('calcDisplay');
            
            if(val === 'C') {
                calcDisplay = '0';
            } else if(val === 'Delete') {
                calcDisplay = calcDisplay.slice(0, -1) || '0';
            } else if(val === '=') {
                try {
                    calcDisplay = Function('"use strict"; return (' + calcDisplay + ')')();
                } catch(e) {
                    calcDisplay = 'Fehler';
                }
            } else {
                if(calcDisplay === '0') {
                    calcDisplay = val;
                } else {
                    calcDisplay += val;
                }
            }
            
            display.value = calcDisplay;
        }

        // ===== EINSTELLUNGEN APP =====
        function showSettings() {
            const body = document.getElementById('modalBody');
            body.innerHTML = `
                <div style="padding: 20px;">
                    <h3>⚙️ Einstellungen</h3>
                    <div style="margin-top: 15px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: 600;">Theme</label>
                        <select style="width: 100%; padding: 8px; border: 1px solid #e0e0e0; border-radius: 4px;">
                            <option>Hell</option>
                            <option>Dunkel</option>
                        </select>
                    </div>
                    <button onclick="alert('Einstellungen gespeichert!')" style="width: 100%; padding: 10px; margin-top: 15px; background: #2c5aa0; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 600;">💾 Speichern</button>
                </div>
            `;
        }

        // ===== PROFIL APP =====
        function showProfile() {
            const body = document.getElementById('modalBody');
            body.innerHTML = `
                <div style="padding: 20px;">
                    <h3>👤 Profil</h3>
                    <div style="margin-top: 15px;">
                        <p><strong>Benutzername:</strong> <?php echo $username; ?></p>
                        <p><strong>Rolle:</strong> <?php echo $role; ?></p>
                        <p><strong>Angemeldet seit:</strong> <?php echo date('d.m.Y H:i:s', strtotime($login_time)); ?></p>
                    </div>
                </div>
            `;
        }

        // ===== HAUPTFUNKTION =====
        function openApp(name) {
            document.getElementById('modalTitle').textContent = name;
            const modal = document.getElementById('appModal');
            modal.classList.add('active');

            if(name === 'Editor') showEditor();
            else if(name === 'Notizen') showNotes();
            else if(name === 'Rechner' || name === 'Taschenrechner') showCalculator();
            else if(name === 'Einstellungen') showSettings();
            else if(name === 'Profil') showProfile();
            else {
                document.getElementById('modalBody').innerHTML = '<p style="padding: 20px;">' + name + ' wird vorbereitet...</p>';
            }
        }
    </script>
</body>
</html>