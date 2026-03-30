<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Queuing System</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .card-hover {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .queue-number {
            font-family: 'Courier New', monospace;
            font-weight: bold;
        }

        footer {
            position: absolute;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="gradient-bg text-white shadow-lg">
        <div class="container mx-auto px-4 py-6">
            <div class="flex justify-between items-center">
                <h1 class="text-3xl font-bold"><i class="fas fa-users mr-3"></i> kristu-jayanti-qms</h1>
                <div class="text-right">
                    <div id="current-time" class="text-xl font-mono"></div>
                    <div class="text-sm">Welcome, Admin</div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8">
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6 card-hover">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                        <i class="fas fa-clock text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-gray-800" id="waiting-count">0</h3>
                        <p class="text-gray-600">Waiting</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6 card-hover">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                        <i class="fas fa-user-check text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-gray-800" id="serving-count">0</h3>
                        <p class="text-gray-600">Serving</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6 card-hover">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                        <i class="fas fa-check-circle text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-gray-800" id="completed-count">0</h3>
                        <p class="text-gray-600">Completed</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6 card-hover">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-red-100 text-red-600 mr-4">
                        <i class="fas fa-times-circle text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-gray-800" id="today-count">0</h3>
                        <p class="text-gray-600">Today's Total</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Add Customer Form -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-lg p-6 card-hover">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6"><i class="fas fa-plus-circle mr-2"></i>Add New Student</h2>
                    
                    <form id="customerForm" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Student Name</label>
                            <input type="text" id="customerName" required 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Enter customer name">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Service Type</label>
                            <select id="serviceType" required 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Select service type</option>
                                <option value="general">General Inquiry</option>
                                <option value="payment">Payment</option>
                                <option value="technical">Technical Support</option>
                                <option value="support">Hostel Support</option>
                                <option value="inquiry">Information Inquiry</option>
                            </select>
                        </div>
                        
                        <button type="submit" 
                                class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 transition duration-300 font-semibold">
                            <i class="fas fa-ticket-alt mr-2"></i>Generate Queue Number
                        </button>
                    </form>
                    
                    <!-- Generated Queue Display -->
                    <div id="queueResult" class="mt-6 hidden">
                        <div class="bg-green-50 border border-green-200 rounded-lg p-6 text-center">
                            <i class="fas fa-check-circle text-green-500 text-4xl mb-3"></i>
                            <h3 class="text-xl font-bold text-gray-800 mb-2">Queue Number Generated</h3>
                            <div class="text-3xl font-bold text-green-600 queue-number mb-2" id="generatedQueue"></div>
                            <p class="text-gray-600">Please wait for your number to be called</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Queue Management -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-lg p-6 card-hover">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-800"><i class="fas fa-list mr-2"></i>Queue Management</h2>
                        <div class="flex space-x-2">
                            <button onclick="refreshQueue()" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition">
                                <i class="fas fa-sync-alt mr-2"></i>Refresh
                            </button>
                        </div>
                    </div>

                    <!-- Counter Status -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-700 mb-3">Counter Status</h3>
                        <div id="countersStatus" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Counters will be loaded here -->
                        </div>
                    </div>

                    <!-- Queue List -->
                    <div class="overflow-x-auto">
                        <table class="w-full table-auto">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Queue No.</th>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Student</th>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Service</th>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Status</th>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Time</th>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="queueTable" class="divide-y divide-gray-200">
                                <!-- Queue data will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
     <!-- <footer class="bg-gray-800 text-white py-6 mt-5">
        <div class="container mx-auto px-4 text-center">
            <p>&copy; 2025 kristu-jayanti-qms. All rights reserved.</p>
        </div>
    </footer> -->

    <script src="js/main.js"></script>
</body>
</html>