// Load attendance report
async function loadReport(event) {
    if (event) {
        event.preventDefault();
    }
    
    const form = document.getElementById('filterForm');
    const formData = new FormData(form);
    
    const params = new URLSearchParams();
    for (let [key, value] of formData.entries()) {
        if (value) {
            params.append(key, value);
        }
    }
    
    const result = await makeRequest(`api/attendance.php?action=get_report&${params.toString()}`);
    
    if (result.success) {
        displayReport(result.data);
    } else {
        showAlert(result.message, 'error');
    }
}

// Load student-specific report
async function loadStudentReport(studentId) {
    const result = await makeRequest(`api/attendance.php?action=get_report&student_id=${studentId}`);
    
    if (result.success) {
        displayReport(result.data);
    } else {
        showAlert('No attendance records found', 'info');
    }
}

// Display report data
function displayReport(data) {
    const reportCard = document.getElementById('reportCard');
    const reportTable = document.getElementById('reportTable');
    const reportSummary = document.getElementById('reportSummary');
    
    if (data.length === 0) {
        showAlert('No records found for the selected filters', 'info');
        reportCard.style.display = 'none';
        return;
    }
    
    // Calculate summary
    const totalRecords = data.length;
    const presentCount = data.filter(r => r.status === 'present').length;
    const absentCount = totalRecords - presentCount;
    const percentage = ((presentCount / totalRecords) * 100).toFixed(2);
    
    reportSummary.innerHTML = `
        <strong>Summary:</strong> 
        Total Records: ${totalRecords} | 
        Present: ${presentCount} | 
        Absent: ${absentCount} | 
        Attendance: ${percentage}%
    `;
    
    // Populate table
    reportTable.innerHTML = '';
    data.forEach(record => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${formatDate(record.date)}</td>
            <td>${record.roll_no}</td>
            <td>${record.student_name}</td>
            <td>${record.course}</td>
            <td>${record.subject}</td>
            <td>${record.teacher_name}</td>
            <td>
                <span class="badge badge-${record.status === 'present' ? 'success' : 'danger'}">
                    ${record.status.charAt(0).toUpperCase() + record.status.slice(1)}
                </span>
            </td>
        `;
        reportTable.appendChild(row);
    });
    
    reportCard.style.display = 'block';
}

// Export report to CSV
function exportReport() {
    const form = document.getElementById('filterForm');
    const formData = new FormData(form);
    
    const params = new URLSearchParams();
    for (let [key, value] of formData.entries()) {
        if (value) {
            params.append(key, value);
        }
    }
    
    window.location.href = `api/attendance.php?action=export_csv&${params.toString()}`;
    showAlert('Report exported successfully', 'success');
}

// Reset filters
function resetFilters() {
    document.getElementById('filterForm').reset();
    document.getElementById('reportCard').style.display = 'none';
}