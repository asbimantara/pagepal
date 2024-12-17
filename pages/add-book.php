<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Array URL gambar default untuk sampul buku
$defaultBookCovers = [
    [
        'url' => 'https://cdn-icons-png.flaticon.com/512/3389/3389081.png', 
        'label' => 'Gambar 1'
    ],
    [
        'url' => 'https://cdn-icons-png.flaticon.com/512/2702/2702154.png',
        'label' => 'Gambar 2'  
    ],
    [
        'url' => 'https://cdn-icons-png.flaticon.com/512/3330/3330314.png',
        'label' => 'Gambar 3'
    ],
    [
        'url' => 'https://cdn-icons-png.flaticon.com/512/3145/3145765.png',
        'label' => 'Gambar 4'
    ],
    [
        'url' => 'https://cdn-icons-png.flaticon.com/512/3771/3771417.png',
        'label' => 'Gambar 5'
    ]
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book = [
        'title' => filter_var($_POST['title'], FILTER_SANITIZE_STRING),
        'author' => filter_var($_POST['author'], FILTER_SANITIZE_STRING),
        'total_pages' => intval($_POST['total_pages']),
        'current_page' => 0,
        'status' => 'belum_mulai',
        'added_date' => new MongoDB\BSON\UTCDateTime(strtotime($_POST['added_date']) * 1000),
        'cover_url' => $_POST['cover_url'],
        'notes' => [],
        'rating' => 0,
        'reading_sessions' => [],
        'last_read' => null
    ];

    try {
        $result = $database->users->updateOne(
            ['_id' => new MongoDB\BSON\ObjectId($_SESSION['user_id'])],
            ['$push' => [
                'books' => [
                    '$each' => [$book],
                    '$position' => 0
                ]
            ]]
        );

        if ($result->getModifiedCount() > 0) {
            header("Location: my-books.php");
            exit();
        }
    } catch (Exception $e) {
        $error = "Gagal menambahkan buku. Silakan coba lagi.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Buku - Book Tracker</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/form.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .cover-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        .cover-option {
            border: 2px solid #ddd;
            border-radius: 8px;
            padding: 0.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            aspect-ratio: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .cover-option:hover {
            border-color: var(--primary-color);
            transform: translateY(-2px);
        }
        .cover-option.selected {
            border-color: var(--primary-color);
            box-shadow: 0 2px 8px rgba(108, 99, 255, 0.2);
        }
        .cover-option img {
            width: 100%;
            height: 100%;
            max-height: 120px;
            object-fit: contain;
            border-radius: 4px;
            margin-bottom: 0.5rem;
            padding: 0.5rem;
        }
        .cover-option p {
            text-align: center;
            margin: 0;
            font-size: 0.9rem;
            color: #666;
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .cover-preview {
            margin-top: 1rem;
            text-align: center;
        }
        .cover-preview img {
            max-width: 200px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .cover-method {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .btn-method {
            padding: 0.5rem 1rem;
            border: 2px solid var(--primary-color);
            background: none;
            color: var(--primary-color);
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-method.active {
            background: var(--primary-color);
            color: white;
        }

        .cover-upload {
            text-align: center;
            padding: 2rem;
            border: 2px dashed #ddd;
            border-radius: 8px;
            margin-top: 1rem;
        }

        .upload-preview {
            margin-top: 1rem;
        }

        .upload-preview img {
            max-width: 200px;
            max-height: 300px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .upload-info {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            color: #666;
        }

        .upload-info i {
            color: var(--primary-color);
            margin-right: 0.5rem;
        }

        .upload-info ul {
            list-style: none;
            margin: 0.5rem 0 0 1.5rem;
        }

        .upload-info li {
            position: relative;
            padding: 0.2rem 0;
        }

        .upload-info li:before {
            content: "•";
            color: var(--primary-color);
            position: absolute;
            left: -1rem;
        }

        .upload-label {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
            padding: 2rem;
            border: 2px dashed #ddd;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .upload-label:hover {
            border-color: var(--primary-color);
            background: #f8f9fa;
        }

        .upload-label i {
            font-size: 2rem;
            color: var(--primary-color);
        }

        input[type="file"] {
            display: none;
        }

        .cover-upload {
            text-align: center;
            margin-top: 1rem;
        }

        .upload-label.highlight {
            border-color: var(--primary-color);
            background-color: rgba(108, 99, 255, 0.1);
        }

        .preview-container {
            position: relative;
            display: inline-block;
            margin-top: 1rem;
        }

        .remove-preview {
            position: absolute;
            top: -10px;
            right: -10px;
            background: #ff4444;
            color: white;
            border: none;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
        }

        .remove-preview:hover {
            background: #cc0000;
            transform: scale(1.1);
        }
    </style>
</head>
<body>
    <?php include '../layouts/header.php'; ?>
    
    <div class="container">
        <div class="form-card">
            <h1>Tambah Buku Baru</h1>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" class="book-form">
                <div class="form-group">
                    <label for="title">Judul Buku <span class="required">*</span></label>
                    <input type="text" 
                           id="title" 
                           name="title" 
                           required 
                           maxlength="100" 
                           placeholder="Masukkan judul buku">
                    <small class="form-text">Maksimal 100 karakter</small>
                </div>

                <div class="form-group">
                    <label for="author">Penulis <span class="required">*</span></label>
                    <input type="text" 
                           id="author" 
                           name="author" 
                           required 
                           maxlength="50" 
                           placeholder="Masukkan nama penulis">
                    <small class="form-text">Maksimal 50 karakter</small>
                </div>

                <div class="form-group">
                    <label for="total_pages">Jumlah Halaman <span class="required">*</span></label>
                    <input type="number" 
                           id="total_pages" 
                           name="total_pages" 
                           required 
                           min="1"
                           max="99999" 
                           placeholder="Masukkan jumlah halaman">
                    <small class="form-text">Maksimal 99.999 halaman</small>
                </div>

                <div class="form-group">
                    <label for="added_date">Waktu Ditambahkan <span class="required">*</span></label>
                    <input type="datetime-local" id="added_date" name="added_date" required>
                </div>

                <div class="form-group">
                    <label>Sampul Buku <span class="required">*</span></label>
                    
                    <!-- Tambahkan pilihan metode cover -->
                    <div class="cover-method">
                        <button type="button" class="btn-method active" onclick="switchMethod('default')">Pilih Sampul Default</button>
                        <button type="button" class="btn-method" onclick="switchMethod('upload')">Upload Sampul</button>
                    </div>

                    <input type="hidden" id="cover_url" name="cover_url" value="<?php echo $defaultBookCovers[0]['url']; ?>">
                    
                    <!-- Container untuk sampul default -->
                    <div id="default-covers" class="cover-options">
                        <?php foreach ($defaultBookCovers as $index => $cover): ?>
                            <div class="cover-option <?php echo $index === 0 ? 'selected' : ''; ?>" 
                                 onclick="selectCover(this, '<?php echo $cover['url']; ?>')">
                                <img src="<?php echo $cover['url']; ?>" alt="<?php echo $cover['label']; ?>">
                                <p><?php echo $cover['label']; ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Container untuk upload sampul -->
                    <div id="upload-cover" class="cover-upload" style="display: none;">
                        <div class="upload-info">
                            <i class="fas fa-info-circle"></i>
                            <p>Untuk hasil terbaik, gunakan foto dengan:</p>
                            <ul>
                                <li>Rasio 2:3 (format buku standar)</li>
                                <li>Minimal 400x600 pixel</li>
                                <li>Format JPG, PNG, atau WEBP</li>
                                <li>Maksimal 2MB</li>
                            </ul>
                        </div>
                        <label for="cover_file" class="upload-label" id="dropZone">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <span>Klik atau seret foto di sini</span>
                        </label>
                        <input type="file" id="cover_file" accept="image/*" onchange="previewUpload(this)">
                        <div class="upload-preview">
                            <div class="preview-container" style="display: none;" id="preview-container">
                                <img id="preview-img" src="#" alt="Preview">
                                <button type="button" class="remove-preview" onclick="removePreview()">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="my-books.php" class="btn-secondary">Batal</a>
                    <button type="submit" class="btn-primary">Tambah Buku</button>
                </div>
            </form>
        </div>
    </div>

    <?php include '../layouts/footer.php'; ?>

    <script>
    function selectCover(element, url) {
        // Hapus class selected dari semua opsi
        document.querySelectorAll('.cover-option').forEach(opt => {
            opt.classList.remove('selected');
        });
        
        // Tambah class selected ke opsi yang dipilih
        element.classList.add('selected');
        
        // Update nilai input hidden
        document.getElementById('cover_url').value = url;
    }

    function switchMethod(method) {
        const defaultCovers = document.getElementById('default-covers');
        const uploadCover = document.getElementById('upload-cover');
        const buttons = document.querySelectorAll('.btn-method');
        
        buttons.forEach(btn => btn.classList.remove('active'));
        
        if (method === 'default') {
            defaultCovers.style.display = 'grid';
            uploadCover.style.display = 'none';
            buttons[0].classList.add('active');
        } else {
            defaultCovers.style.display = 'none';
            uploadCover.style.display = 'block';
            buttons[1].classList.add('active');
        }
    }

    function previewUpload(input) {
        const previewContainer = document.getElementById('preview-container');
        const preview = document.getElementById('preview-img');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.src = e.target.result;
                previewContainer.style.display = 'inline-block';
                document.getElementById('cover_url').value = e.target.result;
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }

    function removePreview() {
        const previewContainer = document.getElementById('preview-container');
        const preview = document.getElementById('preview-img');
        const fileInput = document.getElementById('cover_file');
        
        // Reset preview
        preview.src = '#';
        previewContainer.style.display = 'none';
        
        // Reset file input
        fileInput.value = '';
        
        // Reset cover_url ke default jika perlu
        document.getElementById('cover_url').value = '<?php echo $defaultBookCovers[0]['url']; ?>';
    }

    // Tambahkan kode untuk drag and drop
    const dropZone = document.getElementById('dropZone');

    // Mencegah browser membuka file secara default
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
        document.body.addEventListener(eventName, preventDefaults, false);
    });

    // Highlight drop zone saat file di-drag di atasnya
    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, unhighlight, false);
    });

    function preventDefaults (e) {
        e.preventDefault();
        e.stopPropagation();
    }

    function highlight(e) {
        dropZone.classList.add('highlight');
    }

    function unhighlight(e) {
        dropZone.classList.remove('highlight');
    }

    // Handle dropped files
    dropZone.addEventListener('drop', handleDrop, false);

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;

        if (files.length) {
            const fileInput = document.getElementById('cover_file');
            fileInput.files = files;
            previewUpload(fileInput);
        }
    }

    // Fungsi untuk memvalidasi input
    function validateInput(input) {
        const maxLength = input.getAttribute('maxlength');
        const currentLength = input.value.length;
        const formText = input.nextElementSibling;
        
        // Update counter
        formText.setAttribute('data-count', currentLength);
        formText.setAttribute('data-max', maxLength);
        
        // Jika melebihi maxLength, potong teksnya
        if (currentLength > maxLength) {
            input.value = input.value.slice(0, maxLength); // Memotong input ke panjang maksimal
            formText.style.color = '#dc3545';
            formText.textContent = `Maksimal ${maxLength} karakter`;
            return false;
        } else {
            input.classList.remove('invalid');
            formText.style.color = '#666';
            formText.textContent = `${currentLength}/${maxLength} karakter`;
            return true;
        }
    }

    // Validasi jumlah halaman
    function validatePages(input) {
        const max = parseInt(input.getAttribute('max'));
        const min = parseInt(input.getAttribute('min'));
        const value = parseInt(input.value);
        const formText = input.nextElementSibling;

        // Jika input kosong atau bukan angka
        if (isNaN(value)) {
            input.classList.add('invalid');
            formText.style.color = '#dc3545';
            formText.textContent = 'Masukkan angka yang valid';
            return false;
        }

        // Jika melebihi maksimal
        if (value > max) {
            input.value = max; // Set ke nilai maksimal
            input.classList.add('invalid');
            formText.style.color = '#dc3545';
            formText.textContent = `Maksimal ${max.toLocaleString()} halaman`;
            return false;
        } 
        // Jika kurang dari minimal
        else if (value < min) {
            input.classList.add('invalid');
            formText.style.color = '#dc3545';
            formText.textContent = 'Minimal 1 halaman';
            return false;
        } 
        else {
            input.classList.remove('invalid');
            formText.style.color = '#666';
            formText.textContent = `Maksimal ${max.toLocaleString()} halaman`;
            return true;
        }
    }

    // Event listeners untuk input text dengan validasi keypress
    document.getElementById('title').addEventListener('keypress', function(e) {
        if (this.value.length >= this.getAttribute('maxlength')) {
            e.preventDefault(); // Mencegah pengetikan karakter baru
        }
    });

    document.getElementById('author').addEventListener('keypress', function(e) {
        if (this.value.length >= this.getAttribute('maxlength')) {
            e.preventDefault(); // Mencegah pengetikan karakter baru
        }
    });

    // Event listeners untuk input text dengan validasi paste
    ['title', 'author'].forEach(id => {
        document.getElementById(id).addEventListener('paste', function(e) {
            const maxLength = this.getAttribute('maxlength');
            const pastedText = e.clipboardData.getData('text');
            
            if (this.value.length + pastedText.length > maxLength) {
                e.preventDefault(); // Mencegah paste jika melebihi batas
                alert(`Teks yang di-paste akan melebihi batas ${maxLength} karakter`);
            }
        });
    });

    // Event listener untuk input number dengan validasi input
    document.getElementById('total_pages').addEventListener('input', function(e) {
        const max = parseInt(this.getAttribute('max'));
        const value = this.value;
        
        if (value > max) {
            this.value = max; // Set ke nilai maksimal
        }
    });

    // Event listeners untuk input text
    document.getElementById('title').addEventListener('input', function() {
        validateInput(this);
    });

    document.getElementById('author').addEventListener('input', function() {
        validateInput(this);
    });

    // Event listener untuk input number
    document.getElementById('total_pages').addEventListener('input', function() {
        validatePages(this);
    });

    // Validasi form sebelum submit
    document.querySelector('.book-form').addEventListener('submit', function(e) {
        const title = document.getElementById('title');
        const author = document.getElementById('author');
        const pages = document.getElementById('total_pages');
        
        const titleValid = validateInput(title);
        const authorValid = validateInput(author);
        const pagesValid = validatePages(pages);

        if (!titleValid || !authorValid || !pagesValid) {
            e.preventDefault();
            alert('Mohon periksa kembali input Anda. Pastikan tidak melebihi batas maksimal.');
        }
    });
    </script>
</body>
</html> 