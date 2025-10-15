const express = require('express');
const multer = require('multer');
const path = require('path');
const app = express();

// Set up storage for uploaded files
const storage = multer.diskStorage({
    destination: function (req, file, cb) {
        cb(null, 'uploads/');
    },
    filename: function (req, file, cb) {
        cb(null, file.originalname);
    }
});

const upload = multer({ storage: storage });

// Serve the HTML form
app.get('/', (req, res) => {
    res.sendFile(path.join(__dirname, 'index.html'));
});

// Handle file upload
app.post('/upload', upload.single('file'), (req, res) => {
    res.send('File uploaded successfully!');
});

// Start server
app.listen(3000, () => {
    console.log('Server is running on http://localhost:3000');
});
