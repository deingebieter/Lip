/* ========================================
   L.I.P - Live Interaction Portal
   JavaScript - script.js
   v0.5
   ======================================== */

// ===== DOCUMENT READY =====
document.addEventListener('DOMContentLoaded', function() {
    initializeApp();
});

// ===== INITIALISIERUNG =====
function initializeApp() {
    console.log('L.I.P Portal v0.5 initialisiert');
    
    // Zeit aktualisieren
    updateTime();
    setInterval(updateTime, 50); // Alle 50ms aktualisieren für Millisekunden-Genauigkeit
    
    // Dropdown-Events
    setupDropdowns();
    
    // Modal-Events
    setupModal();
    
    // Weitere Initialisierungen
    setupApplications();
}

// ========================================
// ZEIT-FUNKTIONALITÄT
// ========================================

/**
 * Aktualisiert die Uhr mit Millisekunden-Genauigkeit
 */
function updateTime() {
    const now = new Date();
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const seconds = String(now.getSeconds()).padStart(2, '0');
    const milliseconds = String(now.getMilliseconds()).padStart(3, '0');
    
    const timeString = `${hours}:${minutes}:${seconds}.${milliseconds}`;
    
    const timeDisplay = document.getElementById('timeDisplay');
    if(timeDisplay) {
        timeDisplay.textContent = timeString;
    }
}

// ========================================
// DROPDOWN-FUNKTIONALITÄT
// ========================================

/**
 * Initialisiert alle Dropdown-Events
 */
function setupDropdowns() {
    // ===== UHR-DROPDOWN =====
    const timeDisplay = document.getElementById('timeDisplay');
    const timeDropdown = document.getElementById('timeDropdown');
    
    if(timeDisplay && timeDropdown) {
        timeDisplay.addEventListener('click', function(e) {
            e.stopPropagation();
            closeAllDropdowns();
            timeDropdown.classList.toggle('active');
        });
    }
    
    // ===== BENUTZER-MENÜ =====
    const userMenuBtn = document.getElementById('userMenuBtn');
    const userDropdown = document.getElementById('userDropdown');
    
    if(userMenuBtn && userDropdown) {
        userMenuBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            closeAllDropdowns();
            userDropdown.classList.toggle('active');
        });
    }
    
    // ===== CLICK OUTSIDE SCHLIESSEN =====
    document.addEventListener('click', function() {
        closeAllDropdowns();
    });
    
    // ===== ESC-TASTE =====
    document.addEventListener('keydown', function(e) {
        if(e.key === 'Escape') {
            closeAllDropdowns();
        }
    });
}

/**
 * Schließt alle geöffneten Dropdowns
 */
function closeAllDropdowns() {
    const dropdowns = document.querySelectorAll('[class*="dropdown"]');
    dropdowns.forEach(dropdown => {
        if(dropdown.classList.contains('active')) {
            dropdown.classList.remove('active');
        }
    });
}

// ========================================
// MODAL-FUNKTIONALITÄT
// ========================================

/**
 * Initialisiert Modal-Events
 */
function setupModal() {
    const appModal = document.getElementById('appModal');
    const modalCloseBtn = document.getElementById('modalCloseBtn');
    
    if(modalCloseBtn) {
        modalCloseBtn.addEventListener('click', function() {
            closeModal();
        });
    }
    
    // ===== MODAL SCHLIESSEN BEIM AUSSENCLICK =====
    if(appModal) {
        appModal.addEventListener('click', function(e) {
            if(e.target === appModal) {
                closeModal();
            }
        });
    }
    
    // ===== ESC-TASTE ZUM SCHLIESSEN =====
    document.addEventListener('keydown', function(e) {
        if(e.key === 'Escape' && appModal && appModal.classList.contains('active')) {
            closeModal();
        }
    });
}

/**
 * Schließt das Modal
 */
function closeModal() {
    const appModal = document.getElementById('appModal');
    if(appModal) {
        appModal.classList.remove('active');
    }
}

/**
 * Öffnet das Modal
 */
function openModal() {
    const appModal = document.getElementById('appModal');
    if(appModal) {
        appModal.classList.add('active');
    }
}

// ========================================
// ANWENDUNGEN ÖFFNEN
// ========================================

/**
 * Öffnet eine Anwendung mit Rollen-Kontrolle
 * @param {string} appName - Name der Anwendung
 */
function openApplication(appName) {
    console.log('Öffne Anwendung:', appName);
    
    const appModal = document.getElementById('appModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalBody = document.getElementById('modalBody');
    
    // Benutzer-Rolle abrufen
    const userRole = document.body.getAttribute('data-role');
    const username = document.body.getAttribute('data-username');
    
    const roleHierarchy = {
        'Administrator': 5,
        'Superuser': 4,
        'Master': 3,
        'Slave': 2,
        'Konsument': 1
    };
    
    const currentLevel = roleHierarchy[userRole] || 0;
    
    // Modal-Titel setzen
    modalTitle.textContent = appName;
    
    // Unterschiedliche Inhalte je nach Anwendung
    switch(appName) {
        // ===== EDITOR =====
        case 'Editor':
            renderEditorApp(modalBody);
            break;
            
        // ===== NOTIZEN =====
        case 'Notizen':
            renderNotesApp(modalBody);
            break;
            
        // ===== TASCHENRECHNER =====
        case 'Taschenrechner':
            renderCalculatorApp(modalBody);
            break;
            
        // ===== KONSUMENTEN VERWALTEN =====
        case 'Konsumenten verwalten':
            if(currentLevel < 2) {
                showAccessDenied('Konsumenten verwalten');
                return;
            }
            renderKonsumentenApp(modalBody);
            break;
            
        // ===== SLAVES VERWALTEN =====
        case 'Slaves verwalten':
            if(currentLevel < 3) {
                showAccessDenied('Slaves verwalten');
                return;
            }
            renderSlavesApp(modalBody);
            break;
            
        // ===== BENUTZER VERWALTEN =====
        case 'Benutzer verwalten':
            if(currentLevel < 4) {
                showAccessDenied('Benutzer verwalten');
                return;
            }
            renderUsersApp(modalBody);
            break;
            
        // ===== SYSTEMVERWALTUNG =====
        case 'Systemverwaltung':
            if(currentLevel < 5) {
                showAccessDenied('Systemverwaltung');
                return;
            }
            renderSystemApp(modalBody);
            break;
            
        // ===== EINSTELLUNGEN =====
        case 'Einstellungen':
            renderSettingsApp(modalBody);
            break;
            
        // ===== PROFIL =====
        case 'Profil':
            renderProfileApp(modalBody, username, userRole);
            break;
            
        // ===== MEINE ROLLE =====
        case 'Meine Rolle':
            renderRoleInfoApp(modalBody, userRole, currentLevel);
            break;
            
        default:
            modalBody.innerHTML = `<p>Anwendung: ${appName}</p>`;
    }
    
    // Modal öffnen
    openModal();
}

// ========================================
// APP RENDERER FUNKTIONEN
// ========================================

/**
 * Rendert die Editor-App MIT DOWNLOAD-FUNKTIONEN
 */
function renderEditorApp(container) {
    container.innerHTML = `
        <div class="editor-app">
            <p style="color: var(--text-secondary); font-size: 12px; margin-bottom: 15px;">
                Schreiben Sie Ihren Text hier. Sie können den Inhalt in verschiedenen Formaten herunterladen.
            </p>
            <textarea id="editorContent" placeholder="Geben Sie Ihren Text ein..."></textarea>
            
            <div style="margin-bottom: 15px;">
                <h4 style="font-size: 12px; margin-bottom: 10px; color: var(--text-secondary); text-transform: uppercase;">Download-Format</h4>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(100px, 1fr)); gap: 8px;">
                    <button class="btn-download" onclick="downloadAs('txt');">📄 .TXT</button>
                    <button class="btn-download" onclick="downloadAs('html');">🌐 .HTML</button>
                    <button class="btn-download" onclick="downloadAs('pdf');">📕 .PDF</button>
                    <button class="btn-download" onclick="downloadAs('json');">📊 .JSON</button>
                </div>
            </div>
            
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <button class="btn-save" onclick="clearEditor();">🗑️ Leeren</button>
            </div>
        </div>
    `;
}

/**
 * Download des Editor-Inhalts
 */
function downloadAs(format) {
    const editorContent = document.getElementById('editorContent');
    if(!editorContent) return;
    
    const content = editorContent.value;
    if(!content.trim()) {
        showNotification('Bitte geben Sie zuerst Inhalt ein', 'warning');
        return;
    }
    
    const timestamp = new Date().toISOString().slice(0, 10);
    const filename = `dokument_${timestamp}`;
    
    let fileContent = content;
    let mimeType = 'text/plain';
    let ext = format;
    
    switch(format)
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dokument</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
            line-height: 1.6;
            color: #333;
        }
    </style>
</head>
<body>
    <pre>${escapeHtml(content)}</pre>
    <hr>
    <p style="font-size: 12px; color: #999;">Erstellt: ${new Date().toLocaleString('de-DE')}</p>
</body>
</html>`;
            mimeType = 'text/html';
            break;
            
        case 'pdf':
            showNotification('PDF-Download wird vorbereitet...', 'info');
            fileContent = content;
            mimeType = 'text/plain';
            break;
            
        case 'json':
            fileContent = JSON.stringify({
                content: content,
                created: new Date().toISOString(),
                format: 'text/plain'
            }, null, 2);
            mimeType = 'application/json';
            -bottom: 15px;">
                <textarea id="newNote" placeholder="Neue Notiz eingeben..." style="margin-bottom: 10px;"></textarea>
                <button class="btn-save" onclick="addNote();">➕ Notiz speichern</button>
            </div>
            <div style="border-top: 1px solid var(--border-color); padding-top: 15px;">
                <h3 style="font-size: 14px; margin-bottom: 15px;">💡 Tipp: Notizen werden in dieser Sitzung gespeichert</h3>
                <div class="notes-list" id="notesList">
                    <p style="color: var(--text-secondary);">Noch keine Notizen. Schreiben Sie eine!</p>
                </div>
            </div>
        </div>
    `;
    
    loadNotes();
}

// Session-Notizen (werden beim Schließen gelöscht - KEIN LocalStorage!)
let sessionNotes = [];

/**
 * Fügt eine neue Notiz hinzu
 */
function addNote() {
    const noteInput = document.getElementById('newNote');
    if(!noteInput) return;
    
    const noteText = noteInput.value.trim();
    
    if(!noteText) {
        showNotification('Bitte geben Sie einen Text ein', 'warning');
        return;
    }
    
    sessionNotes.push({
        id: Date.now(),
        text: noteText,
        date: new Date().toLocaleDateString('de-DE'),
        time: new Date().toLocaleTimeString('de-DE')
    });
    
    noteInput.value = '';
    loadNotes();
    showNotification('Notiz gespeichert!', 'success');
}

/**
 * Lädt und zeigt alle Notizen
 */
function loadNotes() {
    const notesList = document.getElementById('notesList');
    
    if(!notesList) return;
    
    if(sessionNotes.length === 0) {
        notesList.innerHTML = '<p style="color: var(--text-secondary);">Noch keine Notizen. Schreiben Sie eine!</p>';
        return;
    }
    
    // Sortiere nach neuestem zuerst
    const sorted = [...sessionNotes].reverse();
    
    notesList.innerHTML = sorted.map(note => `
        <div style="padding: 12px; border: 1px solid var(--border-color); margin-bottom: 10px; border-radius: 4px; background: var(--bg-light);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div style="flex: 1;">
                    <p style="margin: 0 0 5px 0; color: var(--text-primary); word-wrap: break-word;">${escapeHtml(note.text)}</p>
                    <small style="color: var(--text-secondary); font-size: 11px;">${note.date} ${note.time}</small>
                </div>
                <button onclick="deleteNote(${note.id})" style="padding: 3px 8px; background: var(--error-color); color: white; border: none; border-radius: 3px; cursor: pointer; font-size: 11px; white-space: nowrap;">🗑️ Löschen</button>
            </div>
        </div>
    `).join('');
}

/**
 * Löscht eine Notiz
 */
function deleteNote(id) {
    if(!confirm('Möchten Sie diese Notiz wirklich löschen?')) return;
    
    sessionNotes = sessionNotes.filter(note => note.id !== id);
    loadNotes();
    showNotification('Notiz gelöscht!', 'success');
}

/**
 * Rendert die Taschenrechner-App
 */
function renderCalculatorApp(container) {
    container.innerHTML = `
        <div class="calculator-app">
            <div class="calculator-display">
                <input type="text" id="calcDisplay" readonly value="0" style="width: 100%; padding: 15px; font-size: 24px; font-weight: 700; text-align: right; border: none; background: var(--bg-light); border-radius: 4px; color: var(--text-primary); font-family: 'Courier New', monospace;">
            </div>
            
            <div class="calculator-grid">
                <button class="calc-btn calc-clear" onclick="calcClear();">C</button>
                <button class="calc-btn calc-operator" onclick="calcAppend('/');">÷</button>
                <button class="calc-btn calc-operator" onclick="calcAppend('*');">×</button>
                <button class="calc-btn calc-delete" onclick="calcDelete();">⌫</button>
                
                <button class="calc-btn" onclick="calcAppend('7');">7</button>
                <button class="calc-btn" onclick="calcAppend('8');">8</button>
                <button class="calc-btn" onclick="calcAppend('9');">9</button>
                <button class="calc-btn calc-operator" onclick="calcAppend('-');">−</button>
                
                <button class="calc-btn" onclick="calcAppend('4');">4</button>
                <button class="calc-btn" onclick="calcAppend('5');">5</button>
                <button class="calc-btn" onclick="calcAppend('6');">6</button>
                <button class="calc-btn calc-operator" onclick="calcAppend('+');">+</button>
                
                <button class="calc-btn" onclick="calcAppend('1');">1</button>
                <button class="calc-btn" onclick="calcAppend('2');">2</button>
                <button class="calc-btn" onclick="calcAppend('3');">3</button>
                <button class="calc-btn calc-operator" onclick="calcAppend('%');">%</button>
                
                <button class="calc-btn" style="grid-column: span 2;" onclick="calcAppend('0');">0</button>
                <button class="calc-btn" onclick="calcAppend('.');">.</button>
                <button class="calc-btn calc-equals" onclick="calcCalculate();">=</button>
            </div>
        </div>
    `;
}

// ========================================
// TASCHENRECHNER-FUNKTIONEN
// ========================================

let calcExpression = '';

/**
 * Fügt Wert zum Taschenrechner hinzu
 */
function calcAppend(value) {
    const display = document.getElementById('calcDisplay');
    if(!display) return;
    
    if(display.value === '0') {
        display.value = value;
    } else {
        display.value += value;
    }
    calcExpression = display.value;
}

/**
 * Löscht die letzte Ziffer
 */
function calcDelete() {
    const display = document.getElementById('calcDisplay');
    if(!display) return;
    
    display.value = display.value.slice(0, -1) || '0';
    calcExpression = display.value;
}

/**
 * Setzt den Taschenrechner zurück
 */
function calcClear() {
    const display = document.getElementById('calcDisplay');
    if(!display) return;
    
    display.value = '0';
    calcExpression = '';
}

/**
 * Berechnet das Ergebnis
 */
function calcCalculate() {
    const display = document.getElementById('calcDisplay');
    if(!display) return;
    
    try {
        const result = Function('"use strict"; return (' + display.value + ')')();
        display.value = result;
        calcExpression = result;
    } catch(error) {
        display.value = 'Fehler';
        console.error('Rechenfehler:', error);
    }
}

// ========================================
// VERWALTUNGS-FUNKTIONEN
// ========================================

/**
 * Rendert die Konsumenten-Verwaltungs-App
 */
function renderKonsumentenApp(container) {
    container.innerHTML = `
        <div class="admin-panel">
            <h3>👥 Konsumenten-Verwaltung</h3>
            <button class="btn-add" onclick="openCreateKonsument();">➕ Neuer Konsument</button>
            
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Benutzername</th>
                        <th>Erstellt von</th>
                        <th>Erstellt am</th>
                        <th>Status</th>
                        <th>Aktion</th>
                    </tr>
                </thead>
                <tbody id="konsumentenList">
                    <tr>
                        <td>Konsument01</td>
                        <td>Slave01</td>
                        <td>2026-01-29</td>
                        <td><span style="color: var(--success-color); font-weight: 600;">✓ Aktiv</span></td>
                        <td>
                            <button class="btn-action btn-edit" onclick="editKonsument('Konsument01');">Bearbeiten</button>
                            <button class="btn-action btn-delete" onclick="deleteKonsument('Konsument01');">Löschen</button>
                        </td>
                    </tr>
                    <tr>
                        <td>Konsument02</td>
                        <td>Slave01</td>
                        <td>2026-01-29</td>
                        <td><span style="color: var(--success-color); font-weight: 600;">✓ Aktiv</span></td>
                        <td>
                            <button class="btn-action btn-edit" onclick="editKonsument('Konsument02');">Bearbeiten</button>
                            <button class="btn-action btn-delete" onclick="deleteKonsument('Konsument02');">Löschen</button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <p style="font-size: 12px; color: var(--text-secondary); margin-top: 15px;">
                📊 Insgesamt: <strong>2 Konsumenten</strong> | Aktiv: <strong>2</strong>
            </p>
        </div>
    `;
}

/**
 * Rendert die Slaves-Verwaltungs-App
 */
function renderSlavesApp(container) {
    container.innerHTML = `
        <div class="admin-panel">
            <h3>🔗 Slave-Verwaltung</h3>
            <button class="btn-add" onclick="openCreateSlave();">➕ Neuer Slave</button>
            
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Benutzername</th>
                        <th>Erstellt von</th>
                        <th>Konsumenten</th>
                        <th>Status</th>
                        <th>Aktion</th>
                    </tr>
                </thead>
                <tbody id="slavesList">
                    <tr>
                        <td>Slave01</td>
                        <td>Master01</td>
                        <td>2</td>
                        <td><span style="color: var(--success-color); font-weight: 600;">✓ Aktiv</span></td>
                        <td>
                            <button class="btn-action btn-edit" onclick="editSlave('Slave01');">Bearbeiten</button>
                            <button class="btn-action btn-delete" onclick="deleteSlave('Slave01');">Löschen</button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <p style="font-size: 12px; color: var(--text-secondary); margin-top: 15px;">
                📊 Insgesamt: <strong>1 Slave</strong> | Aktiv: <strong>1</strong>
            </p>
        </div>
    `;
}

/**
 * Rendert die Benutzer-Verwaltungs-App
 */
function renderUsersApp(container) {
    container.innerHTML = `
        <div class="admin-panel">
            <h3>👔 Benutzerverwaltung</h3>
            <button class="btn-add" onclick="openCreateUser();">➕ Neuer Benutzer</button>
            
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Benutzername</th>
                        <th>Rolle</th>
                        <th>Erstellt</th>
                        <th>Status</th>
                        <th>Aktion</th>
                    </tr>
                </thead>
                <tbody id="usersList">
                    <tr>
                        <td>Administrator</td>
                        <td><span class="role-badge role-administrator">Administrator</span></td>
                        <td>2026-01-29</td>
                        <td><span style="color: var(--success-color); font-weight: 600;">✓ Aktiv</span></td>
                        <td>
                            <button class="btn-action btn-edit" onclick="editUser('Administrator');">Bearbeiten</button>
                        </td>
                    </tr>
                    <tr>
                        <td>SuperUser01</td>
                        <td><span class="role-badge role-superuser">Superuser</span></td>
                        <td>2026-01-29</td>
                        <td><span style="color: var(--success-color); font-weight: 600;">✓ Aktiv</span></td>
                        <td>
                            <button class="btn-action btn-edit" onclick="editUser('SuperUser01');">Bearbeiten</button>
                            <button class="btn-action btn-delete" onclick="deleteUser('SuperUser01');">Löschen</button>
                        </td>
                    </tr>
                    <tr>
                        <td>Master01</td>
                        <td><span class="role-badge role-master">Master</span></td>
                        <td>2026-01-29</td>
                        <td><span style="color: var(--success-color); font-weight: 600;">✓ Aktiv</span></td>
                        <td>
                            <button class="btn-action btn-edit" onclick="editUser('Master01');">Bearbeiten</button>
                            <button class="btn-action btn-delete" onclick="deleteUser('Master01');">Löschen</button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <p style="font-size: 12px; color: var(--text-secondary); margin-top: 15px;">
                📊 Insgesamt: <strong>3 Benutzer</strong> | Aktiv: <strong>3</strong>
            </p>
        </div>
    `;
}

/**
 * Rendert die Systemverwaltungs-App
 */
function renderSystemApp(container) {
    container.innerHTML = `
        <div class="admin-panel">
            <h3>⚙️ Systemverwaltung</h3>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-bottom: 25px;">
                <div style="background: var(--bg-light); padding: 15px; border-radius: 4px; border-left: 4px solid var(--success-color);">
                    <div style="font-size: 12px; color: var(--text-secondary); margin-bottom: 5px;">System-Status</div>
                    <div style="font-size: 18px; font-weight: 700; color: var(--success-color);">✓ Online</div>
                </div>
                
                <div style="background: var(--bg-light); padding: 15px; border-radius: 4px; border-left: 4px solid var(--accent-color);">
                    <div style="font-size: 12px; color: var(--text-secondary); margin-bottom: 5px;">Benutzer Online</div>
                    <div style="font-size: 18px; font-weight: 700; color: var(--accent-color);">12</div>
                </div>
                
                <div style="background: var(--bg-light); padding: 15px; border-radius: 4px; border-left: 4px solid var(--primary-color);">
                    <div style="font-size: 12px; color: var(--text-secondary); margin-bottom: 5px;">Datenbankgröße</div>
                    <div style="font-size: 18px; font-weight: 700; color: var(--primary-color);">256 MB</div>
                </div>
                
                <div style="background: var(--bg-light); padding: 15px; border-radius: 4px; border-left: 4px solid var(--secondary-color);">
                    <div style="font-size: 12px; color: var(--text-secondary); margin-bottom: 5px;">Letzte Sicherung</div>
                    <div style="font-size: 14px; font-weight: 700; color: var(--secondary-color);">Heute 03:00</div>
                </div>
            </div>
            
            <h4 style="margin-bottom: 15px;">System-Aktionen</h4>
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <button class="btn-save" onclick="systemAction('backup');">💾 Sicherung starten</button>
                <button class="btn-save" onclick="systemAction('optimize');">⚡ Optimieren</button>
                <button class="btn-save" style="background: var(--warning-color);" onclick="systemAction('maintenance');">🔧 Wartungsmodus</button>
                <button class="btn-save" style="background: var(--error-color);" onclick="systemAction('restart');">🔄 Neu starten</button>
            </div>
            
            <h4 style="margin-top: 25px; margin-bottom: 15px;">System-Logs</h4>
            <div style="background: #1a1a1a; color: #00ff00; padding: 15px; border-radius: 4px; font-family: 'Courier New', monospace; font-size: 11px; max-height: 200px; overflow-y: auto;">
                <div>[2026-01-30 14:32:15] System gestartet</div>
                <div>[2026-01-30 14:32:20] Datenbank verbunden</div>
                <div>[2026-01-30 14:32:25] 5 Benutzer angemeldet</div>
                <div>[2026-01-30 14:33:10] Sicherung abgeschlossen</div>
                <div>[2026-01-30 14:35:00] Alle Systeme normal</div>
            </div>
        </div>
    `;
}

/**
 * Rendert die Einstellungs-App
 */
function renderSettingsApp(container) {
    container.innerHTML = `
        <div class="admin-panel">
            <h3>⚙️ Einstellungen</h3>
            
            <div class="setting-item">
                <label>🎨 Design-Theme</label>
                <select id="themeSelect" onchange="changeSetting('theme', this.value)">
                    <option value="light">Hell (Standard)</option>
                    <option value="dark">Dunkel</option>
                </select>
            </div>
            
            <div class="setting-item">
                <label>🌐 Sprache</label>
                <select id="languageSelect" onchange="changeSetting('language', this.value)">
                    <option value="de">Deutsch</option>
                    <option value="en">English</option>
                </select>
            </div>
            
            <div class="setting-item">
                <label>🔔 Benachrichtigungen</label>
                <input type="checkbox" id="notificationsCheck" checked onchange="changeSetting('notifications', this.checked)">
                <span style="margin-left: 10px; font-size: 12px; color: var(--text-secondary);">Aktivieren Sie Benachrichtigungen</span>
            </div>
            
            <div class="setting-item">
                <label>⏱️ Session-Timeout (Minuten)</label>
                <input type="number" id="sessionTimeout" value="60" min="5" max="480" style="width: 100px; padding: 8px; border: 1px solid var(--border-color); border-radius: 4px;">
            </div>
            
            <div class="setting-item">
                <label>🔐 Zwei-Faktor-Authentifizierung</label>
                <input type="checkbox" id="twoFactorCheck" onchange="changeSetting('2fa', this.checked)">
                <span style="margin-left: 10px; font-size: 12px; color: var(--text-secondary);">Zusätzliche Sicherheitsebene</span>
            </div>
            
            <button class="btn-save" onclick="saveSettings();">💾 Einstellungen speichern</button>
            
            <div style="margin-top: 25px; padding: 15px; background: var(--bg-light); border-radius: 4px; border-left: 4px solid var(--accent-color);">
                <h4 style="margin: 0 0 10px 0;">ℹ️ Informationen</h4>
                <p style="margin: 5px 0; font-size: 12px;">
                    <strong>Version:</strong> 0.5<br>
                    <strong>Letztes Update:</strong> 2026-01-30<br>
                    <strong>Datenschutz:</strong> <a href="#">Datenschutzerklärung</a>
                </p>
            </div>
        </div>
    `;
}

/**
 * Rendert die Profil-App
 */
function renderProfileApp(container, username, userRole) {
    container.innerHTML = `
        <div class="admin-panel">
            <h3>👤 Benutzerprofil</h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px;">
                <div>
                    <h4 style="font-size: 12px; color: var(--text-secondary); margin-bottom: 10px; text-transform: uppercase;">Persönliche Informationen</h4>
                    <div style="background: var(--bg-light); padding: 15px; border-radius: 4px;">
                        <p style="margin: 5px 0;"><strong>Benutzername:</strong> ${escapeHtml(username)}</p>
                        <p style="margin: 5px 0;"><strong>Rolle:</strong> <span class="role-badge role-administrator">${escapeHtml(userRole)}</span></p>
                        <p style="margin: 5px 0;"><strong>Angemeldet seit:</strong> ${new Date().toLocaleDateString('de-DE')} ${new Date().toLocaleTimeString('de-DE')}</p>
                    </div>
                </div>
                
                <div>
                    <h4 style="font-size: 12px; color: var(--text-secondary); margin-bottom: 10px; text-transform: uppercase;">Konto-Aktionen</h4>
                    <div style="display: flex; flex-direction: column; gap: 10px;">
                        <button class="btn-save" onclick="changePassword();">🔐 Passwort ändern</button>
                        <button class="btn-save" style="background: var(--accent-color);" onclick="exportData();">📥 Daten exportieren</button>
                    </div>
                </div>
            </div>
        </div>
    `;
}

/**
 * Rendert die Rollen-Info-App
 */
function renderRoleInfoApp(container, userRole, roleLevel) {
    const roleDescriptions = {
        'Administrator': 'Vollständige Kontrolle über das gesamte System. Alle Funktionen sind verfügbar.',
        'Superuser': 'Erweiterte Administrative Rechte und Benutzerverwaltung.',
        'Master': 'Kann Slave-Accounts erstellen und verwalten.',
        'Slave': 'Kann Konsumenten-Accounts erstellen und verwalten.',
        'Konsument': 'Zugriff auf Standardfunktionen: Editor, Notizen und Einstellungen.'
    };
    
    container.innerHTML = `
        <div class="admin-panel">
            <h3>📋 Meine Rolle: ${escapeHtml(userRole)}</h3>
            
            <div style="background: var(--bg-light); padding: 20px; border-radius: 4px; margin-bottom: 25px; border-left: 4px solid var(--accent-color);">
                <p style="margin: 0; font-size: 14px; line-height: 1.6;">
                    ${roleDescriptions[userRole] || 'Willkommen!'}
                </p>
            </div>
            
            <h4 style="margin-bottom: 15px;">Berechtigungslevel: ${roleLevel}/5</h4>
            <div style="background: var(--bg-light); padding: 15px; border-radius: 4px; margin-bottom: 25px;">
                <div style="display: flex; gap: 5px; margin-bottom: 10px;">
                    ${Array.from({length: 5}, (_, i) => `
                        <div style="flex: 1; height: 20px; background: ${i < roleLevel ? 'var(--accent-color)' : 'var(--border-color)'}; border-radius: 3px;"></div>
                    `).join('')}
                </div>
                <p style="font-size: 12px; color: var(--text-secondary); margin: 0;">
                    Autorisierungslevel: <strong>${roleLevel}</strong> von <strong>5</strong>
                </p>
            </div>
            
            <h4 style="margin-bottom: 15px;">✓ Verfügbare Funktionen</h4>
            <ul style="list-style: none; padding: 0;">
                <li style="padding: 8px 0; border-bottom: 1px solid var(--border-color);">✓ Editor (mit Download)</li>
                <li style="padding: 8px 0; border-bottom: 1px solid var(--border-color);">✓ Notizen (mit Speicherung)</li>
                <li style="padding: 8px 0; border-bottom: 1px solid var(--border-color);">✓ Taschenrechner</li>
                ${roleLevel >= 2 ? '<li style="padding: 8px 0; border-bottom: 1px solid var(--border-color);">✓ Konsumenten-Verwaltung</li>' : ''}
                ${roleLevel >= 3 ? '<li style="padding: 8px 0; border-bottom: 1px solid var(--border-color);">✓ Slave-Verwaltung</li>' : ''}
                ${roleLevel >= 4 ? '<li style="padding: 8px 0; border-bottom: 1px solid var(--border-color);">✓ Benutzerverwaltung</li>' : ''}
                ${roleLevel >= 5 ? '<li style="padding: 8px 0; border-bottom: 1px solid var(--border-color);">✓ Systemverwaltung</li>' : ''}
                <li style="padding: 8px 0;">✓ Persönliche Einstellungen</li>
            </ul>
        </div>
    `;
}

// ========================================
// HILFSFUNKTIONEN
// ========================================

/**
 * Zeigt eine Zugriff-verweigert Meldung
 */
function showAccessDenied(feature) {
    const container = document.getElementById('modalBody');
    container.innerHTML = `
        <div style="text-align: center; padding: 40px;">
            <div style="font-size: 48px; margin-bottom: 15px;">🚫</div>
            <h3 style="color: var(--error-color); margin-bottom: 10px;">Zugriff verweigert</h3>
            <p style="color: var(--text-secondary); margin-bottom: 20px;">
                Sie haben keine Berechtigung für "${feature}".
            </p>
            <p style="color: var(--text-secondary); font-size: 12px;">
                Bitte kontaktieren Sie einen Administrator, wenn Sie Zugriff benötigen.
            </p>
        </div>
    `;
    openModal();
}

/**
 * Zeigt eine Benachrichtigung
 */
function showNotification(message, type = 'info') {
    // Erstelle Benachrichtigungselement
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 4px;
        font-size: 13px;
        font-weight: 600;
        z-index: 10000;
        animation: slideIn 0.3s ease;
        max-width: 300px;
    `;
    
    // Farbe basierend auf Typ
    const colors = {
        'success': { bg: '#c8e6c9', color: '#2e7d32' },
        'error': { bg: '#ffcdd2', color: '#d32f2f' },
        'warning': { bg: '#fff9c4', color: '#f57f17' },
        'info': { bg: '#bbdefb', color: '#1565c0' }
    };
    
    const colorSet = colors[type] || colors['info'];
    notification.style.backgroundColor = colorSet.bg;
    notification.style.color = colorSet.color;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Entferne nach 3 Sekunden
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

/**
 * Escaped HTML-Zeichen
 */
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

/**
 * Initialisiert Anwendungen
 */
function setupApplications() {
    // Eventlistener für Anwendungen
    console.log('Anwendungen initialisiert');
}

/**
 * Geht zum Home-Dashboard
 */
function goToHome() {
    location.reload();
}

// ===== PLATZHALTER-FUNKTIONEN =====

function openCreateKonsument() {
    showNotification('Konsumenten-Dialog wird geladen...', 'info');
}

function editKonsument(username) {
    showNotification(`Bearbeite: ${username}`, 'info');
}

function deleteKonsument(username) {
    if(confirm(`Möchten Sie ${username} wirklich löschen?`)) {
        showNotification(`${username} wurde gelöscht!`, 'success');
    }
}

function openCreateSlave() {
    showNotification('Slave-Dialog wird geladen...', 'info');
}

function editSlave(username) {
    showNotification(`Bearbeite: ${username}`, 'info');
}

function deleteSlave(username) {
    if(confirm(`Möchten Sie ${username} wirklich löschen?`)) {
        showNotification(`${username} wurde gelöscht!`, 'success');
    }
}

function openCreateUser() {
    showNotification('Benutzer-Dialog wird geladen...', 'info');
}

function editUser(username) {
    showNotification(`Bearbeite: ${username}`, 'info');
}

function deleteUser(username) {
    if(confirm(`Möchten Sie ${username} wirklich löschen?`)) {
        showNotification(`${username} wurde gelöscht!`, 'success');
    }
}

function systemAction(action) {
    const messages = {
        'backup': 'Sicherung wird gestartet...',
        'optimize': 'Optimierung läuft...',
        'maintenance': 'Wartungsmodus aktiviert',
        'restart': 'System wird neu gestartet...'
    };
    showNotification(messages[action] || 'Aktion wird ausgeführt...', 'info');
}

function changeSetting(setting, value) {
    // Keine LocalStorage - nur Session
    console.log(`Einstellung '${setting}' geändert auf:`, value);
}

function saveSettings() {
    showNotification('Einstellungen gespeichert!', 'success');
}

function changePassword() {
    showNotification('Passwort-Änderung wird vorbereitet...', 'info');
}

function exportData() {
    showNotification('Datenexport wird vorbereitet...', 'info');
}

// ===== CSS ANIMATIONEN =====
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(30px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    @keyframes slideOut {
        from {
            opacity: 1;
            transform: translateX(0);
        }
        to {
            opacity: 0;
            transform: translateX(30px);
        }
    }
`;
document.head.appendChild(style);

console.log('✓ L.I.P JavaScript v0.5 geladen');