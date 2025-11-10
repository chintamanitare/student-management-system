// Submit new announcement
async function submitAnnouncement(event) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    
    const title = formData.get('title');
    const message = formData.get('message');
    
    if (!title || !message) {
        showAlert('Please fill in all required fields', 'error');
        return;
    }
    
    const result = await makeRequest('api/announcements.php?action=add', 'POST', formData);
    
    if (result.success) {
        showAlert(result.message, 'success');
        closeModal('addAnnouncementModal');
        form.reset();
        setTimeout(() => location.reload(), 1500);
    } else {
        showAlert(result.message, 'error');
    }
}

// Delete announcement
async function deleteAnnouncement(id) {
    if (!confirmDelete('Are you sure you want to delete this announcement?')) {
        return;
    }
    
    const result = await makeRequest(`api/announcements.php?action=delete&id=${id}`);
    
    if (result.success) {
        showAlert(result.message, 'success');
        setTimeout(() => location.reload(), 1500);
    } else {
        showAlert(result.message, 'error');
    }
}