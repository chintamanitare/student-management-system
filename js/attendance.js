// Update subject when teacher is selected (for admin)
function updateSubject() {
    const teacherSelect = document.getElementById('teacher_id');
    const subjectInput = document.getElementById('subject');
    
    if (teacherSelect && subjectInput) {
        const selectedOption = teacherSelect.options[teacherSelect.selectedIndex];
        const subject = selectedOption.getAttribute('data-subject');
        
        if (subject) {
            subjectInput.value = subject;
        }
    }
}

// Mark all students present
function markAllPresent() {
    const presentRadios = document.querySelectorAll('input[type="radio"][value="present"]');
    presentRadios.forEach(radio => {
        radio.checked = true;
    });
    showAlert('All students marked as present', 'info');
}

// Mark all students absent
function markAllAbsent() {
    const absentRadios = document.querySelectorAll('input[type="radio"][value="absent"]');
    absentRadios.forEach(radio => {
        radio.checked = true;
    });
    showAlert('All students marked as absent', 'info');
}

// Submit attendance
async function submitAttendance(event) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    
    // Validate date
    const date = formData.get('date');
    if (!date) {
        showAlert('Please select a date', 'error');
        return;
    }
    
    // Validate teacher (for admin)
    const teacherId = formData.get('teacher_id');
    if (!teacherId) {
        showAlert('Please select a teacher', 'error');
        return;
    }
    
    // Validate subject
    const subject = formData.get('subject');
    if (!subject) {
        showAlert('Please enter a subject', 'error');
        return;
    }
    
    // Confirm submission
    if (!confirm('Are you sure you want to submit this attendance?')) {
        return;
    }
    
    // Show loading
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Submitting...';
    submitBtn.disabled = true;
    
    const result = await makeRequest('api/attendance.php?action=submit', 'POST', formData);
    
    submitBtn.textContent = originalText;
    submitBtn.disabled = false;
    
    if (result.success) {
        showAlert(result.message, 'success');
        setTimeout(() => {
            form.reset();
            // Reset to today's date
            document.getElementById('attendance_date').value = new Date().toISOString().split('T')[0];
            // Mark all as present by default
            markAllPresent();
        }, 1500);
    } else {
        showAlert(result.message, 'error');
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    // Set today's date by default
    const dateInput = document.getElementById('attendance_date');
    if (dateInput && !dateInput.value) {
        dateInput.value = new Date().toISOString().split('T')[0];
    }
    
    // Mark all present by default
    markAllPresent();
});