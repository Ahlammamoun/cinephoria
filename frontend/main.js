const { app, BrowserWindow, session } = require('electron');

let mainWindow;

function createWindow() {
  // Créer une fenêtre Electron
  mainWindow = new BrowserWindow({
    width: 800,
    height: 600,
    webPreferences: {
      nodeIntegration: true,
      session: session.fromPartition('persist:my-session')  // Créer une session persistante
    },
  });

  // Charger ton application React
  mainWindow.loadURL('http://localhost:8000');

  mainWindow.on('closed', () => {
    mainWindow = null;
  });
}

app.whenReady().then(createWindow);

app.on('window-all-closed', () => {
  if (process.platform !== 'darwin') {
    app.quit();
  }
});
