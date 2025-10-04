// Event Cards Functionality
class EventCardsManager {
    constructor() {
        this.dots = document.querySelectorAll('.dot');
        this.init();
    }
    
    init() {
        this.dots.forEach((dot, index) => {
            dot.addEventListener('click', () => this.showCard(index));
        });
        
        const viewDetailsButtons = document.querySelectorAll('.btn-view-details');
        viewDetailsButtons.forEach((button, index) => {
            button.addEventListener('click', () => this.handleViewDetails(index));
        });
    }
    
    showCard(cardIndex) {
        this.dots.forEach((dot, index) => {
            dot.classList.toggle('active', index === cardIndex);
        });
        console.log(`Showing card ${cardIndex + 1}`);
    }
    
    handleViewDetails(cardIndex) {
        console.log(`View details clicked for card ${cardIndex + 1}`);
        alert(`Xem chi tiết sự kiện ${cardIndex + 1}`);
    }
}

// Search Functionality
class SearchManager {
    constructor() {
        this.searchInput = document.querySelector('.search-input');
        this.searchBtn = document.querySelector('.search-btn');
        this.init();
    }
    
    init() {
        this.searchBtn?.addEventListener('click', () => this.handleSearch());
        this.searchInput?.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                this.handleSearch();
            }
        });
        
        this.searchInput?.addEventListener('input', (e) => {
            this.handleSearchInput(e.target.value);
        });
    }
    
    handleSearch() {
        const query = this.searchInput?.value.trim();
        if (query) {
            console.log('Searching for:', query);
        }
    }
    
    handleSearchInput(value) {
        console.log('Search input:', value);
    }
}

// Navigation Manager
class NavigationManager {
    constructor() {
        this.navItems = document.querySelectorAll('.nav-item');
        this.init();
    }
    
    init() {
        this.navItems.forEach(item => {
            item.addEventListener('click', (e) => {
                e.preventDefault();
                const category = e.target.closest('.nav-item').querySelector('a').dataset.category;
                this.handleCategoryClick(category, item);
            });
        });
    }
    
    handleCategoryClick(category, clickedItem) {
        this.navItems.forEach(item => item.classList.remove('active'));
        clickedItem.classList.add('active');
        console.log('Category selected:', category);
    }
}

// User Actions Manager
class UserActionsManager {
    constructor() {
        this.createEventBtn = document.querySelector('.btn-create');
        this.ticketsBtn = document.querySelector('.btn-tickets');
        this.userProfile = document.querySelector('.user-profile');
        this.init();
    }
    
    init() {
        this.createEventBtn?.addEventListener('click', () => {
            alert('Tạo sự kiện mới');
        });
        
        this.ticketsBtn?.addEventListener('click', () => {
            alert('Xem vé của tôi');
        });
        
        this.userProfile?.addEventListener('click', () => {
            alert('Menu tài khoản');
        });
    }
}

// Initialize all managers when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new EventCardsManager();
    new SearchManager();
    new NavigationManager();
    new UserActionsManager();
});