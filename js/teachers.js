// Submit new teacher
async function submitTeacher(event) {
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
    
    const result = await makeRequest('api/teachers.php?action=add', 'POST', formData);
    
    if (result.success) {
        showAlert(result.message, 'success');
        closeModal('addTeacherModal');
        form.reset();
        setTimeout(() => location.reload(), 1500);
    } else {
        showAlert(result.message, 'error');
    }
}

// Edit teacher - Load data
async function editTeacher(id) {
    const result = await makeRequest(`api/teachers.php?action=get&id=${id}`);
    
    if (result.success) {
        const teacher = result.data;
        document.getElementById('edit_teacher_id').value = teacher.id;
        document.getElementById('edit_name').value = teacher.name;
        document.getElementById('edit_email').value = teacher.email;
        document.getElementById('edit_subject').value = teacher.subject;
        document.getElementById('edit_phone').value = teacher.phone || '';
        
        openModal('editTeacherModal');
    } else {
        showAlert(result.message, 'error');
    }
}

// Update teacher
async function updateTeacher(event) {
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
    
    const result = await makeRequest('api/teachers.php?action=update', 'POST', formData);
    
    if (result.success) {
        showAlert(result.message, 'success');
        closeModal('editTeacherModal');
        setTimeout(() => location.reload(), 1500);
    } else {
        showAlert(result.message, 'error');
    }
}

// Delete teacher
async function deleteTeacher(id) {
    if (!confirmDelete('Are you sure you want to delete this teacher?')) {
        return;
    }
    
    const result = await makeRequest(`api/teachers.php?action=delete&id=${id}`);
    
    if (result.success) {
        showAlert(result.message, 'success');
        setTimeout(() => location.reload(), 1500);
    } else {
        showAlert(result.message, 'error');
    }
}