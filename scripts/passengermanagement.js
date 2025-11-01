// Passenger Management JavaScript

// Get modal elements
const editModal = document.getElementById('editModal');
const closeEdit = document.getElementById('closeEdit');

// Get all update buttons
const updateButtons = document.querySelectorAll('.update-btn');

// Open edit modal when update button is clicked
updateButtons.forEach(button => {
    button.addEventListener('click', function() {
        const passengerId = this.getAttribute('data-id');
        const firstname = this.getAttribute('data-firstname');
        const lastname = this.getAttribute('data-lastname');
        const identity = this.getAttribute('data-identity');
        const email = this.getAttribute('data-email');
        const specialNeeds = this.getAttribute('data-special-needs');
        
        // Populate form fields
        document.getElementById('edit_passenger_id').value = passengerId;
        document.getElementById('edit_firstname').value = firstname;
        document.getElementById('edit_lastname').value = lastname;
        document.getElementById('edit_identity_number').value = identity;
        document.getElementById('edit_email').value = email;
        document.getElementById('edit_special_needs').value = specialNeeds;
        
        // Show modal
        editModal.style.display = 'flex';
    });
});

// Close edit modal
closeEdit.addEventListener('click', function() {
    editModal.style.display = 'none';
});

// Close modal when clicking outside
window.addEventListener('click', function(event) {
    if (event.target === editModal) {
        editModal.style.display = 'none';
    }
});

// Handle edit form submission
document.getElementById('editPassengerForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    formData.append('action', 'update');
    
    fetch('passenger_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            swal({
                title: 'Success!',
                text: data.message,
                icon: 'success',
            }).then(() => {
                location.reload();
            });
        } else {
            swal({
                title: 'Error!',
                text: data.message,
                icon: 'error',
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        swal({
            title: 'Error!',
            text: 'An error occurred while updating the passenger.',
            icon: 'error',
        });
    });
});
