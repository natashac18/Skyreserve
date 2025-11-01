// Modal elements
const addModal = document.getElementById('addModal');
const editModal = document.getElementById('editModal');
const addButton = document.getElementById('add-button');
const closeAdd = document.getElementById('closeAdd');
const closeEdit = document.getElementById('closeEdit');

// Open Add Modal
addButton.addEventListener('click', () => {
    console.log('Add button clicked');
    addModal.style.display = 'flex';
    addModal.style.alignItems = 'center';
    addModal.style.justifyContent = 'center';
    console.log('Modal display set to:', addModal.style.display);
});

// Close modals
closeAdd.addEventListener('click', () => {
    addModal.style.display = 'none';
    document.getElementById('addFlightForm').reset();
});

closeEdit.addEventListener('click', () => {
    editModal.style.display = 'none';
    document.getElementById('editFlightForm').reset();
});

// Close modal when clicking outside
window.addEventListener('click', (event) => {
    if (event.target === addModal) {
        addModal.style.display = 'none';
        document.getElementById('addFlightForm').reset();
    }
    if (event.target === editModal) {
        editModal.style.display = 'none';
        document.getElementById('editFlightForm').reset();
    }
});

// Add Flight Form Submit
document.getElementById('addFlightForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    formData.append('action', 'add');
    
    try {
        const response = await fetch('flight_handler.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            swal({
                title: "Success!",
                text: result.message,
                icon: "success",
                button: "OK"
            }).then(() => {
                location.reload();
            });
        } else {
            swal({
                title: "Error!",
                text: result.message,
                icon: "error",
                button: "OK"
            });
        }
    } catch (error) {
        swal({
            title: "Error!",
            text: "An error occurred while adding the flight",
            icon: "error",
            button: "OK"
        });
    }
});

// Update buttons - Open Edit Modal (using event delegation)
document.addEventListener('click', (e) => {
    if (e.target.classList.contains('update-btn')) {
        const btn = e.target;
        
        // Populate form with current values
        document.getElementById('edit_flight_id').value = btn.dataset.id;
        document.getElementById('edit_flight_number').value = btn.dataset.flightNumber;
        document.getElementById('edit_origin').value = btn.dataset.origin;
        document.getElementById('edit_destination').value = btn.dataset.destination;
        
        // Convert timestamps to datetime-local format
        document.getElementById('edit_departure').value = formatDateTimeLocal(btn.dataset.departure);
        document.getElementById('edit_arrival').value = formatDateTimeLocal(btn.dataset.arrival);
        
        document.getElementById('edit_standard_price').value = btn.dataset.standardPrice;
        document.getElementById('edit_premium_price').value = btn.dataset.premiumPrice;
        
        editModal.style.display = 'flex';
        editModal.style.alignItems = 'center';
        editModal.style.justifyContent = 'center';
    }
});

// Edit Flight Form Submit
document.getElementById('editFlightForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    formData.append('action', 'update');
    
    try {
        const response = await fetch('flight_handler.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            swal({
                title: "Success!",
                text: result.message,
                icon: "success",
                button: "OK"
            }).then(() => {
                location.reload();
            });
        } else {
            swal({
                title: "Error!",
                text: result.message,
                icon: "error",
                button: "OK"
            });
        }
    } catch (error) {
        swal({
            title: "Error!",
            text: "An error occurred while updating the flight",
            icon: "error",
            button: "OK"
        });
    }
});

// Remove buttons - Delete Flight (using event delegation)
document.addEventListener('click', (e) => {
    if (e.target.classList.contains('remove-btn')) {
        const flightId = e.target.dataset.id;
        
        swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover this flight!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then(async (willDelete) => {
            if (willDelete) {
                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('flight_id', flightId);
                
                try {
                    const response = await fetch('flight_handler.php', {
                        method: 'POST',
                        body: formData
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        swal({
                            title: "Deleted!",
                            text: result.message,
                            icon: "success",
                            button: "OK"
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        swal({
                            title: "Error!",
                            text: result.message,
                            icon: "error",
                            button: "OK"
                        });
                    }
                } catch (error) {
                    swal({
                        title: "Error!",
                        text: "An error occurred while deleting the flight",
                        icon: "error",
                        button: "OK"
                    });
                }
            }
        });
    }
});

// Helper function to format datetime for datetime-local input
function formatDateTimeLocal(timestamp) {
    const date = new Date(timestamp);
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');
    
    return `${year}-${month}-${day}T${hours}:${minutes}`;
}
