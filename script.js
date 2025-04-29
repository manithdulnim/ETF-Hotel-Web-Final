// Fetch all rooms
function loadAllRooms() {
    $.get('api.php', data => {
        displayRooms(data);
    }).fail(error => {
        console.error('Error loading rooms:', error);
        $('#roomData').html('<div class="alert alert-danger">Error loading rooms</div>');
    });
}

// Display rooms in a table
function displayRooms(rooms) {
    let html = `
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Type</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Amenities</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>`;
    
    rooms.forEach(room => {
        html += `
            <tr id="room-${room.id}">
                <td>${room.id}</td>
                <td>${room.type}</td>
                <td>${room.description.substring(0, 50)}...</td>
                <td>$${room.price}</td>
                <td>${JSON.parse(room.amenities).join(', ')}</td>
                <td>
                    <button class="btn btn-sm btn-primary edit-btn" data-id="${room.id}">Edit</button>
                    <button class="btn btn-sm btn-danger delete-btn" data-id="${room.id}">Delete</button>
                </td>
            </tr>`;
    });
    
    html += `</tbody></table>`;
    $('#roomData').html(html);
    
    // Add event listeners to new buttons
    $('.edit-btn').click(function() {
        const roomId = $(this).data('id');
        editRoom(roomId);
    });
    
    $('.delete-btn').click(function() {
        const roomId = $(this).data('id');
        deleteRoom(roomId);
    });
}

// Fetch single room
function fetchRoom() {
    const searchId = $('#searchId').val();
    if (!searchId) {
        $('#roomData').html('<div class="alert alert-warning">Please enter a room ID</div>');
        return;
    }
    
    $.get(`api.php?id=${searchId}`, data => {
        $('#roomData').html(`
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">${data.type}</h5>
                    <h6 class="card-subtitle mb-2 text-muted">${data.id}</h6>
                    <p class="card-text">${data.description}</p>
                    <p class="card-text"><strong>Price:</strong> $${data.price}</p>
                    <p class="card-text"><strong>Amenities:</strong> ${JSON.parse(data.amenities).join(', ')}</p>
                    <button class="btn btn-primary edit-btn" data-id="${data.id}">Edit</button>
                    <button class="btn btn-danger delete-btn" data-id="${data.id}">Delete</button>
                </div>
            </div>
        `);
        
        // Add event listeners
        $('.edit-btn').click(function() {
            editRoom(data.id);
        });
        
        $('.delete-btn').click(function() {
            deleteRoom(data.id);
        });
    }).fail(error => {
        console.error('Error fetching room:', error);
        $('#roomData').html('<div class="alert alert-danger">Room not found</div>');
    });
}

// Add new room
$('#roomForm').submit(function(e) {
    e.preventDefault();
    const formData = {
        id: $('#roomForm input[name="id"]').val(),
        type: $('#roomForm input[name="type"]').val(),
        description: $('#roomForm textarea[name="description"]').val(),
        price: $('#roomForm input[name="price"]').val(),
        amenities: ['WiFi', 'TV', 'AC'], // Default amenities
        image_url: 'img/rooms/default.jpg' // Default image
    };
    
    $.ajax({
        url: 'api.php',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(formData),
        success: () => {
            $('#addModal').modal('hide');
            loadAllRooms();
            $('#roomForm')[0].reset();
            showAlert('Room added successfully!', 'success');
        },
        error: (error) => {
            console.error('Error adding room:', error);
            showAlert('Error adding room', 'danger');
        }
    });
});

// Edit room
function editRoom(roomId) {
    $.get(`api.php?id=${roomId}`, room => {
        // Populate modal with room data
        $('#editModal input[name="id"]').val(room.id);
        $('#editModal input[name="type"]').val(room.type);
        $('#editModal textarea[name="description"]').val(room.description);
        $('#editModal input[name="price"]').val(room.price);
        
        // Show modal
        $('#editModal').modal('show');
    }).fail(error => {
        console.error('Error fetching room for edit:', error);
        showAlert('Error loading room details', 'danger');
    });
}

// Update room
$('#editForm').submit(function(e) {
    e.preventDefault();
    const formData = {
        type: $('#editForm input[name="type"]').val(),
        description: $('#editForm textarea[name="description"]').val(),
        price: $('#editForm input[name="price"]').val(),
        amenities: ['WiFi', 'TV', 'AC'], // Default amenities
        image_url: 'img/rooms/default.jpg' // Default image
    };
    
    const roomId = $('#editForm input[name="id"]').val();
    
    $.ajax({
        url: `api.php?id=${roomId}`,
        type: 'PUT',
        contentType: 'application/json',
        data: JSON.stringify(formData),
        success: () => {
            $('#editModal').modal('hide');
            loadAllRooms();
            showAlert('Room updated successfully!', 'success');
        },
        error: (error) => {
            console.error('Error updating room:', error);
            showAlert('Error updating room', 'danger');
        }
    });
});

// Delete room
function deleteRoom(roomId) {
    if (!confirm('Are you sure you want to delete this room?')) return;
    
    $.ajax({
        url: `api.php?id=${roomId}`,
        type: 'DELETE',
        success: () => {
            loadAllRooms();
            showAlert('Room deleted successfully!', 'success');
        },
        error: (error) => {
            console.error('Error deleting room:', error);
            showAlert('Error deleting room', 'danger');
        }
    });
}

// Show alert message
function showAlert(message, type) {
    const alert = $(`
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `);
    
    $('#alertsContainer').append(alert);
    setTimeout(() => alert.alert('close'), 3000);
}

// Initial load
$(document).ready(function() {
    loadAllRooms();
    
    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
});