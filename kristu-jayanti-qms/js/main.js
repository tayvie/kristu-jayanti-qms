// Update current time
function updateTime() {
    const now = new Date();
    document.getElementById('current-time').textContent = 
        now.toLocaleTimeString() + ' - ' + now.toLocaleDateString();
}
setInterval(updateTime, 1000);
updateTime();

// Form submission
document.getElementById('customerForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const name = document.getElementById('customerName').value;
    const serviceType = document.getElementById('serviceType').value;
    
    fetch('api/add_customer.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ name: name, service_type: serviceType })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('generatedQueue').textContent = data.queue_number;
            document.getElementById('queueResult').classList.remove('hidden');
            document.getElementById('customerForm').reset();
            refreshQueue();
            refreshStats();
            
            // Auto-hide success message after 5 seconds
            setTimeout(() => {
                document.getElementById('queueResult').classList.add('hidden');
            }, 5000);
        } else {
            showError(data.message || 'Failed to generate queue number');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showError('An error occurred while generating queue number');
    });
});

// Refresh queue data
function refreshQueue() {
    fetch('api/get_queue.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                updateQueueTable(data.customers);
                updateCounters(data.counters);
            } else {
                showError(data.message || 'Failed to load queue data');
            }
        })
        .catch(error => {
            console.error('Error loading queue:', error);
            showError('Failed to load queue data: ' + error.message);
        });
}

// Update queue table
function updateQueueTable(customers) {
    const table = document.getElementById('queueTable');
    
    if (!customers || customers.length === 0) {
        table.innerHTML = `
            <tr>
                <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                    <i class="fas fa-inbox text-4xl mb-2 block"></i>
                    No customers in queue
                </td>
            </tr>
        `;
        return;
    }
    
    table.innerHTML = '';
    
    customers.forEach(customer => {
        const row = document.createElement('tr');
        row.className = 'hover:bg-gray-50';
        
        let statusClass = '';
        let statusText = '';
        switch(customer.status) {
            case 'waiting': 
                statusClass = 'bg-yellow-100 text-yellow-800';
                statusText = 'Waiting';
                break;
            case 'serving': 
                statusClass = 'bg-blue-100 text-blue-800';
                statusText = 'Serving';
                break;
            case 'completed': 
                statusClass = 'bg-green-100 text-green-800';
                statusText = 'Completed';
                break;
            case 'cancelled': 
                statusClass = 'bg-red-100 text-red-800';
                statusText = 'Cancelled';
                break;
        }
        
        row.innerHTML = `
            <td class="px-4 py-3">
                <span class="queue-number text-lg font-bold">${customer.queue_number}</span>
            </td>
            <td class="px-4 py-3">${customer.name}</td>
            <td class="px-4 py-3">
                <span class="px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                    ${customer.service_type}
                </span>
            </td>
            <td class="px-4 py-3">
                <span class="px-3 py-1 rounded-full text-sm font-medium ${statusClass}">
                    ${statusText}
                </span>
            </td>
            <td class="px-4 py-3 text-sm text-gray-500">
                ${new Date(customer.created_at).toLocaleTimeString()}
            </td>
            <td class="px-4 py-3">
                <div class="flex space-x-2">
                    ${customer.status === 'waiting' ? `
                        <button onclick="callCustomer(${customer.id})" 
                                class="bg-green-500 text-white px-3 py-1 rounded text-sm hover:bg-green-600 transition duration-200">
                            <i class="fas fa-bullhorn mr-1"></i>Call
                        </button>
                    ` : ''}
                    ${customer.status === 'serving' ? `
                        <button onclick="completeCustomer(${customer.id})" 
                                class="bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600 transition duration-200">
                            <i class="fas fa-check mr-1"></i>Complete
                        </button>
                    ` : ''}
                    ${customer.status !== 'completed' && customer.status !== 'cancelled' ? `
                        <button onclick="cancelCustomer(${customer.id})" 
                                class="bg-red-500 text-white px-3 py-1 rounded text-sm hover:bg-red-600 transition duration-200">
                            <i class="fas fa-times mr-1"></i>Cancel
                        </button>
                    ` : ''}
                </div>
            </td>
        `;
        table.appendChild(row);
    });
}

// Update counters status
function updateCounters(counters) {
    const container = document.getElementById('countersStatus');
    
    if (!counters || counters.length === 0) {
        container.innerHTML = '<div class="text-center text-gray-500">No counters configured</div>';
        return;
    }
    
    container.innerHTML = '';
    
    counters.forEach(counter => {
        const counterDiv = document.createElement('div');
        counterDiv.className = `border rounded-lg p-4 ${counter.is_online ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200'}`;
        
        counterDiv.innerHTML = `
            <div class="flex justify-between items-center mb-2">
                <h4 class="font-semibold">${counter.name}</h4>
                <span class="px-2 py-1 rounded text-xs ${counter.is_online ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800'}">
                    ${counter.is_online ? 'Online' : 'Offline'}
                </span>
            </div>
            <div class="text-sm text-gray-600 mb-2">
                Services: ${Array.isArray(counter.service_types) ? counter.service_types.join(', ') : 'General'}
            </div>
            <div class="text-sm">
                ${counter.current_customer_name ? 
                    `Serving: <span class="font-bold">${counter.current_customer_name}</span>` : 
                    'Available'}
            </div>
        `;
        container.appendChild(counterDiv);
    });
}

// Customer actions
function callCustomer(customerId) {
    fetch('api/call_customer.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ customer_id: customerId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            refreshQueue();
            refreshStats();
        } else {
            showError(data.message || 'Failed to call customer');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showError('Failed to call customer');
    });
}

function completeCustomer(customerId) {
    fetch('api/complete_customer.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ customer_id: customerId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            refreshQueue();
            refreshStats();
        } else {
            showError(data.message || 'Failed to complete customer');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showError('Failed to complete customer');
    });
}

function cancelCustomer(customerId) {
    if (confirm('Are you sure you want to cancel this customer?')) {
        fetch('api/cancel_customer.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ customer_id: customerId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                refreshQueue();
                refreshStats();
            } else {
                showError(data.message || 'Failed to cancel customer');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('Failed to cancel customer');
        });
    }
}

// Refresh stats
function refreshStats() {
    fetch('api/get_stats.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const stats = data.data;
                document.getElementById('waiting-count').textContent = stats.waiting;
                document.getElementById('serving-count').textContent = stats.serving;
                document.getElementById('completed-count').textContent = stats.completed;
                document.getElementById('today-count').textContent = stats.today_total;
            } else {
                showError(data.message || 'Failed to load statistics');
            }
        })
        .catch(error => {
            console.error('Error loading stats:', error);
            showError('Failed to load statistics');
        });
}

// Show error message
function showError(message) {
    // Create or show error notification
    let errorDiv = document.getElementById('errorNotification');
    if (!errorDiv) {
        errorDiv = document.createElement('div');
        errorDiv.id = 'errorNotification';
        errorDiv.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
        document.body.appendChild(errorDiv);
    }
    
    errorDiv.innerHTML = `
        <div class="flex items-center">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            <span>${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (errorDiv.parentElement) {
            errorDiv.remove();
        }
    }, 5000);
}

// Auto-refresh every 10 seconds
setInterval(() => {
    refreshQueue();
    refreshStats();
}, 10000);

// Initial load
refreshQueue();
refreshStats();