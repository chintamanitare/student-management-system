// Submit new student
async function submitStudent(event) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    
    // Validation
    const email = formData.get('email');
    const password = formData.get('password');
    const phone = formData.get('phone');
    
    if (!validateEmail(email)) {
        showAlert('Please enter a valid email address', 'error');
        return;
    }
    
    if (password.length < 6) {
        showAlert('Password must be at least 6 characters', 'error');
        return;
    }
    
    if (phone && !validatePhone(phone)) {
        showAlert('Please enter a valid phone number', 'error');
        return;
    }
    
    const result = await makeRequest('api/students.php?action=add', 'POST', formData);
    
    if (result.success) {
        showAlert(result.message, 'success');
        closeModal('addStudentModal');
        form.reset();
        setTimeout(() => location.reload(), 1500);
    } else {
        showAlert(result.message, 'error');
    }
}

// Edit student - Load data
async function editStudent(id) {
    const result = await makeRequest(`api/students.php?action=get&id=${id}`);
    
    if (result.success) {
        const student = result.data;
        document.getElementById('edit_student_id').value = student.id;
        document.getElementById('edit_name').value = student.name;
        document.getElementById('edit_roll_no').value = student.roll_no;
        document.getElementById('edit_email').value = student.email || '';
        document.getElementById('edit_phone').value = student.phone || '';
        document.getElementById('edit_course').value = student.course;
        document.getElementById('edit_batch').value = student.batch;
        
        openModal('editStudentModal');
    } else {
        showAlert(result.message, 'error');
    }
}

// Update student
async function updateStudent(event) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    
    // Validation
    const email = formData.get('email');
    const phone = formData.get('phone');
    
    if (!validateEmail(email)) {
        showAlert('Please enter a valid email address', 'error');
        return;
    }
    
    if (phone && !validatePhone(phone)) {
        showAlert('Please enter a valid phone number', 'error');
        return;
    }
    
    const result = await makeRequest('api/students.php?action=update', 'POST', formData);
    
    if (result.success) {
        showAlert(result.message, 'success');
        closeModal('editStudentModal');
        setTimeout(() => location.reload(), 1500);
    } else {
        showAlert(result.message, 'error');
    }
}

// Delete student
async function deleteStudent(id) {
    if (!confirmDelete('Are you sure you want to delete this student? This will also delete all their attendance records.')) {
        return;
    }
    
    const result = await makeRequest(`api/students.php?action=delete&id=${id}`);
    
    if (result.success) {
        showAlert(result.message, 'success');
        setTimeout(() => location.reload(), 1500);
    } else {
        showAlert(result.message, 'error');
    }
}