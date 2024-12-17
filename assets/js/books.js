function updateProgress(bookIndex) {
  const modal = document.getElementById("progressModal");
  const bookIndexInput = document.getElementById("bookIndex");

  bookIndexInput.value = bookIndex;
  modal.style.display = "block";
}

function closeModal() {
  const modal = document.getElementById("progressModal");
  modal.style.display = "none";
}

// Tutup modal ketika user klik di luar modal
window.onclick = function (event) {
  const modal = document.getElementById("progressModal");
  if (event.target == modal) {
    modal.style.display = "none";
  }
};

let searchTimeout;

function searchBooks() {
    clearTimeout(searchTimeout);
    
    const searchText = document.getElementById('searchBook').value.toLowerCase();
    const bookCards = document.querySelectorAll('.book-card');
    const activeFilter = document.querySelector('.filter-option.active')?.dataset.status || 'all';
    
    // Jika kurang dari 2 karakter, tampilkan semua buku sesuai filter aktif
    if (searchText.length < 2) {
        bookCards.forEach(card => {
            const status = card.dataset.status;
            const matchesFilter = activeFilter === 'all' || status === activeFilter;
            card.style.display = matchesFilter ? '' : 'none';
            card.classList.remove('active');
        });
        const booksContainer = document.querySelector('.books-grid');
        new BooksPagination(booksContainer);
        return;
    }
    
    searchTimeout = setTimeout(() => {
        bookCards.forEach(card => {
            const title = card.querySelector('h3').textContent.toLowerCase();
            const author = card.querySelector('.author').textContent.toLowerCase();
            const status = card.dataset.status;
            
            const matchesSearch = title.includes(searchText) || author.includes(searchText);
            const matchesFilter = activeFilter === 'all' || status === activeFilter;
            
            card.style.display = matchesSearch && matchesFilter ? '' : 'none';
            card.classList.remove('active');
        });

        // Reinisialisasi pagination setelah search
        const booksContainer = document.querySelector('.books-grid');
        new BooksPagination(booksContainer);
    }, 300); // Delay 300ms untuk menghindari terlalu banyak pembaruan
}

function displayResults(results) {
    const resultsContainer = document.querySelector('.search-results');
    resultsContainer.innerHTML = '';
    
    if (results.length === 0) {
        resultsContainer.innerHTML = `
            <div class="no-results">
                <i class="fas fa-search"></i>
                <p>Tidak ada hasil yang ditemukan</p>
            </div>
        `;
    } else {
        results.forEach(result => {
            const statusText = {
                'belum_mulai': 'Belum Mulai',
                'sedang_dibaca': 'Sedang Dibaca',
                'selesai': 'Selesai'
            }[result.status];

            const resultItem = document.createElement('div');
            resultItem.className = 'search-result-item';
            resultItem.innerHTML = `
                <div class="result-content">
                    <img src="${result.cover_url}" alt="${result.title}" class="result-cover">
                    <div class="result-info">
                        <h4>${result.title}</h4>
                        <p>${result.author}</p>
                        <div class="result-meta">
                            <span class="status-badge ${result.status}">${statusText}</span>
                            <span class="progress-badge">${result.progress}%</span>
                        </div>
                    </div>
                </div>
            `;
            resultItem.onclick = () => window.location.href = `book-detail.php?index=${result.index}`;
            resultsContainer.appendChild(resultItem);
        });
    }
    
    resultsContainer.classList.add('active');
}

// Tutup hasil pencarian ketika klik di luar
document.addEventListener('click', (e) => {
    const resultsContainer = document.querySelector('.search-results');
    const searchContainer = document.querySelector('.search-container');
    
    if (!searchContainer.contains(e.target)) {
        resultsContainer.classList.remove('active');
    }
});

// Global variable untuk pagination
let currentPagination = null;

class BooksPagination {
    constructor(container, itemsPerPage = 12) {
        this.container = container;
        this.itemsPerPage = itemsPerPage;
        this.currentPage = 1;
        this.items = Array.from(container.querySelectorAll('.book-card:not([style*="display: none"])')); // Hanya ambil kartu yang visible
        this.totalPages = Math.ceil(this.items.length / this.itemsPerPage);
        
        // Hapus pagination yang ada
        const existingPagination = document.querySelector('.pagination');
        if (existingPagination) {
            existingPagination.remove();
        }
        
        this.init();
    }

    init() {
        if (this.items.length > 0) {
            this.createPaginationControls();
            this.showPage(1);
        }
    }

    createPaginationControls() {
        const paginationDiv = document.createElement('div');
        paginationDiv.className = 'pagination';

        // Prev button
        const prevButton = document.createElement('button');
        prevButton.className = 'pagination-button';
        prevButton.innerHTML = '<i class="fas fa-chevron-left"></i>';
        prevButton.onclick = () => this.prevPage();

        // Next button
        const nextButton = document.createElement('button');
        nextButton.className = 'pagination-button';
        nextButton.innerHTML = '<i class="fas fa-chevron-right"></i>';
        nextButton.onclick = () => this.nextPage();

        // Page buttons
        const pageButtons = document.createElement('div');
        pageButtons.className = 'pagination-pages';

        // Info text
        const infoText = document.createElement('span');
        infoText.className = 'pagination-info';

        paginationDiv.appendChild(prevButton);
        paginationDiv.appendChild(pageButtons);
        paginationDiv.appendChild(infoText);
        paginationDiv.appendChild(nextButton);

        this.container.after(paginationDiv);
        this.paginationControls = {
            prevButton,
            nextButton,
            pageButtons,
            infoText
        };

        this.updatePaginationButtons();
    }

    updatePaginationButtons() {
        const { prevButton, nextButton, pageButtons, infoText } = this.paginationControls;
        
        // Update prev/next buttons
        prevButton.disabled = this.currentPage === 1;
        nextButton.disabled = this.currentPage === this.totalPages;

        // Update page buttons
        pageButtons.innerHTML = '';
        for (let i = 1; i <= this.totalPages; i++) {
            const button = document.createElement('button');
            button.className = `pagination-button ${i === this.currentPage ? 'active' : ''}`;
            button.textContent = i;
            button.onclick = () => this.showPage(i);
            pageButtons.appendChild(button);
        }

        // Update info text
        const startIndex = (this.currentPage - 1) * this.itemsPerPage + 1;
        const endIndex = Math.min(startIndex + this.itemsPerPage - 1, this.items.length);
        infoText.textContent = `Menampilkan ${startIndex}-${endIndex} dari ${this.items.length} buku`;
    }

    showPage(pageNumber) {
        this.currentPage = pageNumber;
        const startIndex = (pageNumber - 1) * this.itemsPerPage;
        const endIndex = startIndex + this.itemsPerPage;

        // Reset semua kartu
        this.items.forEach(item => {
            item.classList.remove('active');
        });

        // Tampilkan kartu untuk halaman ini
        this.items.slice(startIndex, endIndex).forEach(item => {
            item.classList.add('active');
        });

        this.updatePaginationButtons();
    }

    prevPage() {
        if (this.currentPage > 1) {
            this.showPage(this.currentPage - 1);
        }
    }

    nextPage() {
        if (this.currentPage < this.totalPages) {
            this.showPage(this.currentPage + 1);
        }
    }
}

function filterBooks(status) {
    const bookCards = document.querySelectorAll('.book-card');
    const searchText = document.getElementById('searchBook').value.toLowerCase();
    
    // Reset dan filter kartu
    bookCards.forEach(card => {
        const bookStatus = card.dataset.status;
        const title = card.querySelector('h3').textContent.toLowerCase();
        const author = card.querySelector('.author').textContent.toLowerCase();
        
        const matchesStatus = status === 'all' || bookStatus === status;
        const matchesSearch = !searchText || title.includes(searchText) || author.includes(searchText);
        
        // Gunakan display none/block untuk filter
        card.style.display = matchesStatus && matchesSearch ? '' : 'none';
        card.classList.remove('active');
    });

    // Reinisialisasi pagination setelah filter
    const booksContainer = document.querySelector('.books-grid');
    new BooksPagination(booksContainer);
}

// Inisialisasi awal
document.addEventListener('DOMContentLoaded', function() {
    const booksContainer = document.querySelector('.books-grid');
    if (booksContainer && booksContainer.querySelectorAll('.book-card').length > 0) {
        new BooksPagination(booksContainer);
    }
    
    // ... kode event listener lainnya ...
});

// Event listeners for filter functionality
document.addEventListener('DOMContentLoaded', function() {
    const filterButton = document.getElementById('filterButton');
    const filterDropdown = document.getElementById('filterDropdown');
    const filterOptions = document.querySelectorAll('.filter-option');
    
    // Toggle dropdown
    filterButton.addEventListener('click', function(e) {
        e.stopPropagation();
        filterDropdown.classList.toggle('show');
    });

    // Handle filter option selection
    filterOptions.forEach(option => {
        option.addEventListener('click', function() {
            filterBooks(this.dataset.status);
            filterDropdown.classList.remove('show');
        });
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!filterDropdown.contains(e.target) && !filterButton.contains(e.target)) {
            filterDropdown.classList.remove('show');
        }
    });

    const booksContainer = document.querySelector('.books-grid');
    if (booksContainer && booksContainer.querySelectorAll('.book-card').length > 0 && !currentPagination) {
        currentPagination = new BooksPagination(booksContainer);
    }
});

// Tambahkan event listener untuk input search
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchBook');
    if (searchInput) {
        searchInput.addEventListener('input', searchBooks);
    }
    // ... kode event listener lainnya ...
});
