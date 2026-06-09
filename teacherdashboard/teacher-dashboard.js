// Teacher Dashboard JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Sample subjects data (in real app, this would come from API)
    let subjects = [
        {
            id: 1,
            name: "Operating Systems",
            code: "CS301",
            description: "Fundamentals of operating systems and process management",
            color: "#4361ee",
            quizzes: 12,
            students: 45,
            averageScore: 85
        },
        {
            id: 2,
            name: "Database Management",
            code: "CS302",
            description: "Database design, SQL, and transaction management",
            color: "#f72585",
            quizzes: 8,
            students: 38,
            averageScore: 78
        },
        {
            id: 3,
            name: "Scripting Languages",
            code: "CS303",
            description: "Python, JavaScript and shell scripting",
            color: "#4cc9f0",
            quizzes: 15,
            students: 52,
            averageScore: 92
        },
        {
            id: 4,
            name: "Numerical Methods",
            code: "MATH401",
            description: "Numerical analysis and computational mathematics",
            color: "#7209b7",
            quizzes: 6,
            students: 28,
            averageScore: 76
        },
        {
            id: 5,
            name: "Software Engineering",
            code: "CS401",
            description: "Software development methodologies and practices",
            color: "#faa307",
            quizzes: 10,
            students: 41,
            averageScore: 88
        }
    ];

    // DOM Elements
    const sidebar = document.querySelector('.sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const subjectsContainer = document.getElementById('subjectsContainer');
    const emptyState = document.getElementById('emptyState');
    const viewButtons = document.querySelectorAll('.view-btn');
    const addSubjectBtn = document.getElementById('addSubjectBtn');
    const addFirstSubjectBtn = document.getElementById('addFirstSubject');
    const addSubjectModal = new bootstrap.Modal(document.getElementById('addSubjectModal'));
    const saveSubjectBtn = document.getElementById('saveSubjectBtn');
    const addSubjectForm = document.getElementById('addSubjectForm');
    const colorPreview = document.getElementById('colorPreview');
    const subjectColor = document.getElementById('subjectColor');

    // Initialize dashboard
    initDashboard();

    // Sidebar toggle
    sidebarToggle.addEventListener('click', function() {
        sidebar.classList.toggle('collapsed');
    });

    // View toggle
    viewButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const view = this.dataset.view;
            viewButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            subjectsContainer.classList.toggle('list-view', view === 'list');
        });
    });

    // Add subject buttons
    addSubjectBtn.addEventListener('click', () => addSubjectModal.show());
    addFirstSubjectBtn.addEventListener('click', () => addSubjectModal.show());

    // Color preview
    subjectColor.addEventListener('input', function() {
        colorPreview.style.backgroundColor = this.value;
    });

    // Save subject
    saveSubjectBtn.addEventListener('click', function() {
        const formData = new FormData(addSubjectForm);
        const subjectName = document.getElementById('subjectName').value.trim();
        const subjectCode = document.getElementById('subjectCode').value.trim();
        const subjectDescription = document.getElementById('subjectDescription').value.trim();
        const subjectColor = document.getElementById('subjectColor').value;

        if (!subjectName) {
            showAlert('Please enter a subject name', 'error');
            return;
        }

        const newSubject = {
            id: Date.now(),
            name: subjectName,
            code: subjectCode,
            description: subjectDescription,
            color: subjectColor,
            quizzes: 0,
            students: 0,
            averageScore: 0
        };

        subjects.push(newSubject);
        renderSubjects();
        updateStats();
        addSubjectModal.hide();
        addSubjectForm.reset();
        
        showAlert('Subject added successfully!', 'success');
    });

    // Initialize functions
    function initDashboard() {
        renderSubjects();
        updateStats();
        initCharts();
        setupEventListeners();
    }

    function renderSubjects() {
        if (subjects.length === 0) {
            subjectsContainer.style.display = 'none';
            emptyState.style.display = 'block';
            return;
        }

        subjectsContainer.style.display = 'grid';
        emptyState.style.display = 'none';

        subjectsContainer.innerHTML = subjects.map(subject => `
            <div class="subject-card" data-subject-id="${subject.id}">
                <div class="subject-header">
                    <div class="subject-icon" style="background: linear-gradient(135deg, ${subject.color}, ${lightenColor(subject.color, 20)})">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="subject-actions">
                        <button class="action-btn edit-subject" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="action-btn delete-subject" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <h4 class="subject-title">${subject.name}</h4>
                <p class="subject-code">${subject.code}</p>
                <p class="subject-description">${subject.description}</p>
                <div class="subject-stats">
                    <div class="stat">
                        <span class="stat-value">${subject.quizzes}</span>
                        <span class="stat-label">Quizzes</span>
                    </div>
                    <div class="stat">
                        <span class="stat-value">${subject.students}</span>
                        <span class="stat-label">Students</span>
                    </div>
                    <div class="stat">
                        <span class="stat-value">${subject.averageScore}%</span>
                        <span class="stat-label">Avg Score</span>
                    </div>
                </div>
            </div>
        `).join('');

        // Add event listeners to subject cards
        document.querySelectorAll('.subject-card').forEach(card => {
            card.addEventListener('click', function(e) {
                if (!e.target.closest('.subject-actions')) {
                    const subjectId = this.dataset.subjectId;
                    window.location.href = `questions.php?subject_id=${subjectId}`;
                }
            });
        });

        // Add event listeners to action buttons
        document.querySelectorAll('.edit-subject').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const subjectId = this.closest('.subject-card').dataset.subjectId;
                editSubject(parseInt(subjectId));
            });
        });

        document.querySelectorAll('.delete-subject').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const subjectId = this.closest('.subject-card').dataset.subjectId;
                deleteSubject(parseInt(subjectId));
            });
        });
    }

    function updateStats() {
        document.getElementById('totalSubjects').textContent = subjects.length;
        document.getElementById('totalQuizzes').textContent = subjects.reduce((sum, subject) => sum + subject.quizzes, 0);
        document.getElementById('totalStudents').textContent = subjects.reduce((sum, subject) => sum + subject.students, 0);
    }

    function editSubject(subjectId) {
        const subject = subjects.find(s => s.id === subjectId);
        if (subject) {
            document.getElementById('subjectName').value = subject.name;
            document.getElementById('subjectCode').value = subject.code;
            document.getElementById('subjectDescription').value = subject.description;
            document.getElementById('subjectColor').value = subject.color;
            colorPreview.style.backgroundColor = subject.color;
            
            // Change modal title and button text
            document.querySelector('#addSubjectModal .modal-title').textContent = 'Edit Subject';
            document.getElementById('saveSubjectBtn').textContent = 'Update Subject';
            
            // Remove existing subject and update
            const tempSaveHandler = function() {
                subjects = subjects.filter(s => s.id !== subjectId);
                saveSubjectBtn.click();
                saveSubjectBtn.removeEventListener('click', tempSaveHandler);
            };
            
            saveSubjectBtn.addEventListener('click', tempSaveHandler);
            addSubjectModal.show();
        }
    }

    function deleteSubject(subjectId) {
        if (confirm('Are you sure you want to delete this subject? All associated quizzes will also be deleted.')) {
            subjects = subjects.filter(s => s.id !== subjectId);
            renderSubjects();
            updateStats();
            showAlert('Subject deleted successfully', 'success');
        }
    }

    function initCharts() {
        // Performance Chart
        const performanceCtx = document.getElementById('performanceChart').getContext('2d');
        new Chart(performanceCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Average Score',
                    data: [75, 78, 82, 85, 83, 87],
                    borderColor: '#4361ee',
                    backgroundColor: 'rgba(67, 97, 238, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        min: 70,
                        max: 100
                    }
                }
            }
        });

        // Completion Chart
        const completionCtx = document.getElementById('completionChart').getContext('2d');
        new Chart(completionCtx, {
            type: 'doughnut',
            data: {
                labels: ['Completed', 'In Progress', 'Not Started'],
                datasets: [{
                    data: [65, 20, 15],
                    backgroundColor: [
                        '#4361ee',
                        '#4cc9f0',
                        '#e9ecef'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

    function setupEventListeners() {
        // Search functionality
        const searchInput = document.querySelector('.search-box input');
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const filteredSubjects = subjects.filter(subject => 
                subject.name.toLowerCase().includes(searchTerm) ||
                subject.code.toLowerCase().includes(searchTerm) ||
                subject.description.toLowerCase().includes(searchTerm)
            );
            
            if (searchTerm) {
                renderFilteredSubjects(filteredSubjects);
            } else {
                renderSubjects();
            }
        });

        // Notification bell
        document.querySelector('.notification-bell').addEventListener('click', function() {
            showAlert('You have 3 new notifications', 'info');
        });
    }

    function renderFilteredSubjects(filteredSubjects) {
        if (filteredSubjects.length === 0) {
            subjectsContainer.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-search"></i>
                    <h4>No subjects found</h4>
                    <p>Try adjusting your search terms</p>
                </div>
            `;
            return;
        }

        // Similar to renderSubjects but with filtered data
        subjectsContainer.innerHTML = filteredSubjects.map(subject => `
            <div class="subject-card" data-subject-id="${subject.id}">
                <div class="subject-header">
                    <div class="subject-icon" style="background: linear-gradient(135deg, ${subject.color}, ${lightenColor(subject.color, 20)})">
                        <i class="fas fa-book"></i>
                    </div>
                </div>
                <h4 class="subject-title">${subject.name}</h4>
                <p class="subject-code">${subject.code}</p>
                <p class="subject-description">${subject.description}</p>
                <div class="subject-stats">
                    <div class="stat">
                        <span class="stat-value">${subject.quizzes}</span>
                        <span class="stat-label">Quizzes</span>
                    </div>
                    <div class="stat">
                        <span class="stat-value">${subject.students}</span>
                        <span class="stat-label">Students</span>
                    </div>
                </div>
            </div>
        `).join('');
    }

    // Utility functions
    function lightenColor(color, percent) {
        const num = parseInt(color.replace("#", ""), 16);
        const amt = Math.round(2.55 * percent);
        const R = (num >> 16) + amt;
        const G = (num >> 8 & 0x00FF) + amt;
        const B = (num & 0x0000FF) + amt;
        return "#" + (0x1000000 + (R < 255 ? R < 1 ? 0 : R : 255) * 0x10000 +
            (G < 255 ? G < 1 ? 0 : G : 255) * 0x100 +
            (B < 255 ? B < 1 ? 0 : B : 255)).toString(16).slice(1);
    }

    function showAlert(message, type) {
        // Remove existing alerts
        const existingAlert = document.querySelector('.custom-alert');
        if (existingAlert) {
            existingAlert.remove();
        }
        
        // Create alert element
        const alert = document.createElement('div');
        alert.className = `custom-alert alert-${type}`;
        alert.innerHTML = `
            <div class="alert-content">
                <i class="fas fa-${getAlertIcon(type)}"></i>
                <span>${message}</span>
                <button class="alert-close">&times;</button>
            </div>
        `;
        
        // Add styles
        alert.style.cssText = `
            position: fixed;
            top: 100px;
            right: 30px;
            background: ${getAlertColor(type)};
            color: white;
            padding: 15px 20px;
            border-radius: 12px;
            box-shadow: var(--shadow-lg);
            z-index: 10000;
            max-width: 400px;
            animation: slideInRight 0.3s ease;
            border-left: 4px solid ${getAlertBorderColor(type)};
        `;
        
        document.body.appendChild(alert);
        
        // Add close functionality
        const closeBtn = alert.querySelector('.alert-close');
        closeBtn.addEventListener('click', () => {
            alert.style.animation = 'slideOutRight 0.3s ease';
            setTimeout(() => alert.remove(), 300);
        });
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (alert.parentNode) {
                alert.style.animation = 'slideOutRight 0.3s ease';
                setTimeout(() => alert.remove(), 300);
            }
        }, 5000);
    }

    function getAlertIcon(type) {
        const icons = {
            'success': 'check-circle',
            'error': 'exclamation-circle',
            'info': 'info-circle',
            'warning': 'exclamation-triangle'
        };
        return icons[type] || 'info-circle';
    }

    function getAlertColor(type) {
        const colors = {
            'success': '#4cd964',
            'error': '#ff3b30',
            'info': '#007aff',
            'warning': '#ffcc00'
        };
        return colors[type] || '#007aff';
    }

    function getAlertBorderColor(type) {
        const colors = {
            'success': '#2ecc71',
            'error': '#e74c3c',
            'info': '#3498db',
            'warning': '#f39c12'
        };
        return colors[type] || '#3498db';
    }

    // Add CSS animations
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
        
        .alert-content {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .alert-close {
            background: none;
            border: none;
            color: white;
            font-size: 1.2rem;
            cursor: pointer;
            margin-left: auto;
            opacity: 0.8;
            transition: opacity 0.3s ease;
        }
        
        .alert-close:hover {
            opacity: 1;
        }
    `;
    document.head.appendChild(style);
});