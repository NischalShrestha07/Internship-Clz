<div>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        indigo: {
                            600: '#4F46E5',
                            700: '#4338CA',
                            800: '#3730A3',
                            900: '#2F2E41',
                        },
                        blue: {
                            700: '#1D4ED8',
                            800: '#1E40AF',
                        },
                        emerald: {
                            500: '#10B981',
                        },
                    },
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'Helvetica Neue', 'Arial', 'sans-serif'],
                    },
                    animation: {
                        'shake': 'shake 0.5s cubic-bezier(.36,.07,.19,.97) both',
                    },
                    keyframes: {
                        shake: {
                            '10%, 90%': { transform: 'translateX(-1px)' },
                            '20%, 80%': { transform: 'translateX(2px)' },
                            '30%, 50%, 70%': { transform: 'translateX(-4px)' },
                            '40%, 60%': { transform: 'translateX(4px)' },
                        },
                    },
                },
            },
        };
    </script>

    <!-- Google Fonts and Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Symbols+Outlined" rel="stylesheet">

    <!-- Include Navbar -->
    @include('livewire.main.navbar')

    <!-- Global Styles -->
    <style>
        html {
            scroll-behavior: smooth;
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.8s ease-out forwards;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-float {
            animation: float 5s ease-in-out infinite;
        }

        .animate-float-delay {
            animation: float 5s ease-in-out infinite 1.5s;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-15px);
            }
        }

        .feature-card:hover .feature-icon {
            transform: rotate(12deg) scale(1.15);
        }

        .service-card:hover {
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
        }

        .accordion-content {
            transition: max-height 0.3s ease-out, opacity 0.3s ease-out;
        }

        .accordion-content.hidden {
            max-height: 0;
            opacity: 0;
            overflow: hidden;
        }

        .accordion-content:not(.hidden) {
            max-height: 500px;
            opacity: 1;
        }

        .progress-ring {
            background: conic-gradient(#10B981 0% var(--progress), #4F46E5 0%);
            transition: background 0.3s ease;
        }
    </style>

    <!-- Hero Section -->
    <section id="home" class="bg-gradient-to-br from-indigo-600 to-blue-700 text-white py-32 relative overflow-hidden">
        <div class="absolute inset-0 bg-black opacity-50"></div>
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <h1 class="text-5xl sm:text-7xl font-extrabold mb-6 leading-tight animate-fade-in-up">
                <span class="bg-clip-text text-transparent bg-gradient-to-r from-emerald-500 to-blue-300">Elevate
                    HR</span><br>with HRMS
            </h1>
            <p class="text-xl sm:text-2xl mb-10 max-w-4xl mx-auto text-blue-100 leading-relaxed">
                Transform your HR operations with a powerful, intuitive platform for hiring, payroll, and employee
                engagement.
            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-6">
                <a href="#contact"
                    class="px-10 py-4 bg-emerald-500 text-white rounded-xl hover:bg-emerald-600 font-semibold text-lg transition-all duration-300 transform hover:scale-105 shadow-xl"
                    aria-label="Get started with HRMS">
                    Try Free Demo
                </a>
                <a href="#features"
                    class="px-10 py-4 bg-indigo-800 bg-opacity-90 text-white rounded-xl hover:bg-opacity-100 font-semibold text-lg transition-all duration-300 transform hover:scale-105 shadow-xl"
                    aria-label="Explore HRMS features">
                    Discover Features
                </a>
            </div>
        </div>
        <!-- Animated Background Elements -->
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0">
            <div
                class="absolute top-10 left-20 w-48 h-48 bg-emerald-500 rounded-full filter blur-3xl opacity-20 animate-float">
            </div>
            <div
                class="absolute bottom-20 right-16 w-72 h-72 bg-blue-500 rounded-full filter blur-3xl opacity-20 animate-float-delay">
            </div>
        </div>
        <img src="https://images.unsplash.com/photo-1551434678-e076c223a692?ixlib=rb-4.0.3&auto=format&fit=crop&w=1350&q=80"
            alt="Modern office collaboration" class="absolute inset-0 w-full h-full object-cover opacity-10">
    </section>

    <!-- Trusted By Section -->
    <section class="bg-white py-10 border-b border-gray-200">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <p class="text-center text-gray-600 text-lg mb-8 font-medium">Trusted by Industry Leaders</p>
            <div class="flex flex-wrap justify-center items-center gap-6 md:gap-12 opacity-80">
                <img src="https://images.unsplash.com/photo-1611162617474-5b21e879e113?ixlib=rb-4.0.3&auto=format&fit=crop&w=150&q=80"
                    alt="TechCorp Logo" class="h-10 md:h-12 hover:opacity-100 transition-opacity">
                <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?ixlib=rb-4.0.3&auto=format&fit=crop&w=150&q=80"
                    alt="GrowEasy Logo" class="h-10 md:h-12 hover:opacity-100 transition-opacity">
                <img src="{{ asset('itsn.png') }}" alt="Innovate Inc Logo"
                    class="h-10 md:h-12 hover:opacity-100 transition-opacity">
                <img src="https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?ixlib=rb-4.0.3&auto=format&fit=crop&w=150&q=80"
                    alt="ScaleUp Logo" class="h-10 md:h-12 hover:opacity-100 transition-opacity">
                <img src="https://images.unsplash.com/photo-1593642632823-8f785ba67e45?ixlib=rb-4.0.3&auto=format&fit=crop&w=150&q=80"
                    alt="Nexlify Logo" class="h-10 md:h-12 hover:opacity-100 transition-opacity">
            </div>
        </div>
    </section>

    <!-- About Us Section -->
    <section id="about" class="py-24 bg-gray-50">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span
                    class="inline-block px-4 py-1.5 text-sm font-semibold text-indigo-600 bg-indigo-100 rounded-full mb-4">About
                    Us</span>
                <h2 class="text-5xl font-bold text-gray-900 mb-4">Empowering HR Excellence</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                    HRMS delivers cutting-edge solutions to streamline HR operations, boost employee engagement, and
                    drive business success.
                </p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div class="space-y-8">
                    <div class="flex items-start gap-4 service-card">
                        <div class="flex-shrink-0 bg-indigo-600 p-4 rounded-lg text-white">
                            <span class="material-symbols-outlined text-3xl">group</span>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">Our Mission</h3>
                            <p class="text-gray-600">
                                To provide innovative HR tools that empower businesses to focus on people, not
                                processes.
                            </p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4 service-card">
                        <div class="flex-shrink-0 bg-indigo-600 p-4 rounded-lg text-white">
                            <span class="material-symbols-outlined text-3xl">lightbulb</span>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">Our Vision</h3>
                            <p class="text-gray-600">
                                To redefine HR management with seamless, scalable, and secure technology.
                            </p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4 service-card">
                        <div class="flex-shrink-0 bg-indigo-600 p-4 rounded-lg text-white">
                            <span class="material-symbols-outlined text-3xl">favorite</span>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">Our Values</h3>
                            <p class="text-gray-600">
                                Excellence, Innovation, and Collaboration are at the heart of our platform.
                            </p>
                        </div>
                    </div>
                    <a href="#contact"
                        class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold transition-all duration-300 transform hover:scale-105"
                        aria-label="Contact us to learn more about HRMS">
                        Connect with Us
                        <span class="ml-2 material-symbols-outlined">arrow_forward</span>
                    </a>
                </div>
                <div class="relative">
                    <img src="/itsn.png" alt="HR team working on dashboard"
                        class="rounded-xl shadow-2xl w-full h-auto transform hover:scale-105 transition duration-500">
                    <div
                        class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-indigo-900 to-transparent p-6 text-white rounded-b-xl">
                        <p class="text-lg font-medium">"HRMS reduced our onboarding time by 50%."</p>
                        <p class="text-blue-200 mt-2">- Lisa Carter, HR Lead</p>
                    </div>
                    <div
                        class="absolute -top-4 -left-4 bg-emerald-500 text-white p-4 rounded-lg shadow-lg hidden md:block">
                        <p class="font-bold text-xl">15K+</p>
                        <p class="text-sm">Organizations Served</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Key Features Section -->
    <section id="features" class="py-24 bg-white">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span
                    class="inline-block px-4 py-1.5 text-sm font-semibold text-indigo-600 bg-indigo-100 rounded-full mb-4">Key
                    Features</span>
                <h2 class="text-5xl font-bold text-gray-900 mb-4">All-in-One HR Solution</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                    From recruitment to analytics, HRMS has you covered with powerful tools.
                </p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div
                    class="bg-white p-8 rounded-xl shadow-lg backdrop-blur-md bg-opacity-30 border border-gray-100 feature-card transition-all duration-300">
                    <div
                        class="bg-indigo-600 w-16 h-16 rounded-lg flex items-center justify-center mb-6 feature-icon transition-transform duration-300 text-white">
                        <span class="material-symbols-outlined text-3xl">fingerprint</span>
                    </div>
                    <h3 class="text-2xl font-semibold text-gray-900 mb-3">Biometric Attendance</h3>
                    <p class="text-gray-600 mb-4">
                        Secure and accurate tracking with fingerprint and facial recognition.
                    </p>
                    <ul class="space-y-2 text-gray-600">
                        <li class="flex items-center"><span
                                class="material-symbols-outlined text-emerald-500 mr-2">check_circle</span>Real-time
                            tracking</li>
                        <li class="flex items-center"><span
                                class="material-symbols-outlined text-emerald-500 mr-2">check_circle</span>Geo-fencing
                        </li>
                        <li class="flex items-center"><span
                                class="material-symbols-outlined text-emerald-500 mr-2">check_circle</span>Mobile
                            check-in</li>
                    </ul>
                </div>
                <div
                    class="bg-white p-8 rounded-xl shadow-lg backdrop-blur-md bg-opacity-30 border border-gray-100 feature-card transition-all duration-300">
                    <div
                        class="bg-indigo-600 w-16 h-16 rounded-lg flex items-center justify-center mb-6 feature-icon transition-transform duration-300 text-white">
                        <span class="material-symbols-outlined text-3xl">payments</span>
                    </div>
                    <h3 class="text-2xl font-semibold text-gray-900 mb-3">Automated Payroll</h3>
                    <p class="text-gray-600 mb-4">
                        Streamlined payroll with compliance and direct deposit features.
                    </p>
                    <ul class="space-y-2 text-gray-600">
                        <li class="flex items-center"><span
                                class="material-symbols-outlined text-emerald-500 mr-2">check_circle</span>Multi-country
                            taxes</li>
                        <li class="flex items-center"><span
                                class="material-symbols-outlined text-emerald-500 mr-2">check_circle</span>Custom pay
                            components</li>
                        <li class="flex items-center"><span
                                class="material-symbols-outlined text-emerald-500 mr-2">check_circle</span>Reimbursements
                        </li>
                    </ul>
                </div>
                <div
                    class="bg-white p-8 rounded-xl shadow-lg backdrop-blur-md bg-opacity-30 border border-gray-100 feature-card transition-all duration-300">
                    <div
                        class="bg-indigo-600 w-16 h-16 rounded-lg flex items-center justify-center mb-6 feature-icon transition-transform duration-300 text-white">
                        <span class="material-symbols-outlined text-3xl">work</span>
                    </div>
                    <h3 class="text-2xl font-semibold text-gray-900 mb-3">Talent Management</h3>
                    <p class="text-gray-600 mb-4">
                        Build top teams with recruitment and performance tools.
                    </p>
                    <ul class="space-y-2 text-gray-600">
                        <li class="flex items-center"><span
                                class="material-symbols-outlined text-emerald-500 mr-2">check_circle</span>Applicant
                            tracking</li>
                        <li class="flex items-center"><span
                                class="material-symbols-outlined text-emerald-500 mr-2">check_circle</span>360-degree
                            feedback</li>
                        <li class="flex items-center"><span
                                class="material-symbols-outlined text-emerald-500 mr-2">check_circle</span>Succession
                            planning</li>
                    </ul>
                </div>
                <div
                    class="bg-white p-8 rounded-xl shadow-lg backdrop-blur-md bg-opacity-30 border border-gray-100 feature-card transition-all duration-300">
                    <div
                        class="bg-indigo-600 w-16 h-16 rounded-lg flex items-center justify-center mb-6 feature-icon transition-transform duration-300 text-white">
                        <span class="material-symbols-outlined text-3xl">self_improvement</span>
                    </div>
                    <h3 class="text-2xl font-semibold text-gray-900 mb-3">Employee Self-Service</h3>
                    <p class="text-gray-600 mb-4">
                        Empower employees with easy access to HR services.
                    </p>
                    <ul class="space-y-2 text-gray-600">
                        <li class="flex items-center"><span
                                class="material-symbols-outlined text-emerald-500 mr-2">check_circle</span>Leave
                            management</li>
                        <li class="flex items-center"><span
                                class="material-symbols-outlined text-emerald-500 mr-2">check_circle</span>Payslip
                            access</li>
                        <li class="flex items-center"><span
                                class="material-symbols-outlined text-emerald-500 mr-2">check_circle</span>Benefits
                            enrollment</li>
                    </ul>
                </div>
                <div
                    class="bg-white p-8 rounded-xl shadow-lg backdrop-blur-md bg-opacity-30 border border-gray-100 feature-card transition-all duration-300">
                    <div
                        class="bg-indigo-600 w-16 h-16 rounded-lg flex items-center justify-center mb-6 feature-icon transition-transform duration-300 text-white">
                        <span class="material-symbols-outlined text-3xl">analytics</span>
                    </div>
                    <h3 class="text-2xl font-semibold text-gray-900 mb-3">Advanced Analytics</h3>
                    <p class="text-gray-600 mb-4">
                        Optimize HR with data-driven insights and dashboards.
                    </p>
                    <ul class="space-y-2 text-gray-600">
                        <li class="flex items-center"><span
                                class="material-symbols-outlined text-emerald-500 mr-2">check_circle</span>Custom
                            dashboards</li>
                        <li class="flex items-center"><span
                                class="material-symbols-outlined text-emerald-500 mr-2">check_circle</span>Predictive
                            analytics</li>
                        <li class="flex items-center"><span
                                class="material-symbols-outlined text-emerald-500 mr-2">check_circle</span>Exportable
                            reports</li>
                    </ul>
                </div>
                <div
                    class="bg-white p-8 rounded-xl shadow-lg backdrop-blur-md bg-opacity-30 border border-gray-100 feature-card transition-all duration-300">
                    <div
                        class="bg-indigo-600 w-16 h-16 rounded-lg flex items-center justify-center mb-6 feature-icon transition-transform duration-300 text-white">
                        <span class="material-symbols-outlined text-3xl">security</span>
                    </div>
                    <h3 class="text-2xl font-semibold text-gray-900 mb-3">Enterprise Security</h3>
                    <p class="text-gray-600 mb-4">
                        Protect data with top-tier security and compliance.
                    </p>
                    <ul class="space-y-2 text-gray-600">
                        <li class="flex items-center"><span
                                class="material-symbols-outlined text-emerald-500 mr-2">check_circle</span>Role-based
                            access</li>
                        <li class="flex items-center"><span
                                class="material-symbols-outlined text-emerald-500 mr-2">check_circle</span>GDPR/CCPA
                            compliance</li>
                        <li class="flex items-center"><span
                                class="material-symbols-outlined text-emerald-500 mr-2">check_circle</span>Audit trails
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits Section -->
    <section class="py-24 bg-gradient-to-r from-indigo-700 to-blue-800 text-white">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span
                    class="inline-block px-4 py-1.5 text-sm font-semibold text-emerald-500 bg-white bg-opacity-20 rounded-full mb-4">Benefits</span>
                <h2 class="text-5xl font-bold text-white mb-4">Revolutionize Your HR</h2>
                <p class="text-xl text-blue-100 max-w-3xl mx-auto leading-relaxed">
                    Unlock efficiency, engagement, and compliance with HRMS.
                </p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div
                    class="bg-white bg-opacity-10 p-8 rounded-xl backdrop-blur-md border border-white border-opacity-20 service-card transition-all duration-300">
                    <span class="material-symbols-outlined text-4xl text-emerald-500 mb-4">speed</span>
                    <h3 class="text-xl font-semibold text-white mb-3">75% Time Savings</h3>
                    <p class="text-blue-100">
                        Automate HR tasks to focus on strategic growth.
                    </p>
                </div>
                <div
                    class="bg-white bg-opacity-10 p-8 rounded-xl backdrop-blur-md border border-white border-opacity-20 service-card transition-all duration-300">
                    <span class="material-symbols-outlined text-4xl text-emerald-500 mb-4">savings</span>
                    <h3 class="text-xl font-semibold text-white mb-3">50% Cost Reduction</h3>
                    <p class="text-blue-100">
                        Eliminate manual processes and reduce expenses.
                    </p>
                </div>
                <div
                    class="bg-white bg-opacity-10 p-8 rounded-xl backdrop-blur-md border border-white border-opacity-20 service-card transition-all duration-300">
                    <span class="material-symbols-outlined text-4xl text-emerald-500 mb-4">mood</span>
                    <h3 class="text-xl font-semibold text-white mb-3">3x Engagement</h3>
                    <p class="text-blue-100">
                        Boost employee satisfaction with self-service tools.
                    </p>
                </div>
                <div
                    class="bg-white bg-opacity-10 p-8 rounded-xl backdrop-blur-md border border-white border-opacity-20 service-card transition-all duration-300">
                    <span class="material-symbols-outlined text-4xl text-emerald-500 mb-4">gpp_good</span>
                    <h3 class="text-xl font-semibold text-white mb-3">Full Compliance</h3>
                    <p class="text-blue-100">
                        Stay compliant with automated legal updates.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="py-24 bg-gray-50">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span
                    class="inline-block px-4 py-1.5 text-sm font-semibold text-indigo-600 bg-indigo-100 rounded-full mb-4">Get
                    Started</span>
                <h2 class="text-5xl font-bold text-gray-900 mb-4">Effortless Onboarding</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                    Launch HRMS in four simple steps with our expert support.
                </p>
            </div>
            <div class="relative">
                <div
                    class="hidden lg:block absolute left-1/2 top-0 h-full w-1 bg-indigo-200 transform -translate-x-1/2">
                </div>
                <div class="space-y-12 lg:space-y-0">
                    <div class="relative lg:flex lg:items-center lg:justify-between service-card">
                        <div class="lg:w-5/12 lg:pr-12 mb-8 lg:mb-0 text-right">
                            <div class="lg:hidden absolute -left-8 top-0 w-4 h-4 bg-indigo-600 rounded-full z-10"></div>
                            <h3 class="text-2xl font-semibold text-gray-900 mb-2">Discovery Call</h3>
                            <p class="text-gray-600">
                                We analyze your HR needs to customize the platform.
                            </p>
                        </div>
                        <div class="hidden lg:block w-2/12 text-center">
                            <div
                                class="mx-auto w-16 h-16 bg-indigo-600 rounded-full flex items-center justify-center text-white text-xl font-bold">
                                1</div>
                        </div>
                        <div class="lg:w-5/12 lg:pl-12">
                            <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
                                <span
                                    class="material-symbols-outlined text-indigo-600 text-4xl mb-4">calendar_today</span>
                                <p class="text-gray-600">
                                    30-minute consultation with our HR experts.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="relative lg:flex lg:items-center lg:justify-between service-card">
                        <div class="lg:w-5/12 lg:pr-12 order-3 mb-8 lg:mb-0">
                            <div class="lg:hidden absolute -left-8 top-0 w-4 h-4 bg-indigo-600 rounded-full z-10"></div>
                            <h3 class="text-2xl font-semibold text-gray-900 mb-2">System Setup</h3>
                            <p class="text-gray-600">
                                Full configuration and data migration by our team.
                            </p>
                        </div>
                        <div class="hidden lg:block w-2/12 text-center order-2">
                            <div
                                class="mx-auto w-16 h-16 bg-indigo-600 rounded-full flex items-center justify-center text-white text-xl font-bold">
                                2</div>
                        </div>
                        <div class="lg:w-5/12 lg:pl-12 order-1 mb-8 lg:mb-0">
                            <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
                                <span class="material-symbols-outlined text-indigo-600 text-4xl mb-4">settings</span>
                                <p class="text-gray-600">
                                    Seamless setup tailored to your specifications.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="relative lg:flex lg:items-center lg:justify-between service-card">
                        <div class="lg:w-5/12 lg:pr-12 mb-8 lg:mb-0 text-right">
                            <div class="lg:hidden absolute -left-8 top-0 w-4 h-4 bg-indigo-600 rounded-full z-10"></div>
                            <h3 class="text-2xl font-semibold text-gray-900 mb-2">Training</h3>
                            <p class="text-gray-600">
                                Comprehensive training for seamless adoption.
                            </p>
                        </div>
                        <div class="hidden lg:block w-2/12 text-center">
                            <div
                                class="mx-auto w-16 h-16 bg-indigo-600 rounded-full flex items-center justify-center text-white text-xl font-bold">
                                3</div>
                        </div>
                        <div class="lg:w-5/12 lg:pl-12">
                            <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
                                <span class="material-symbols-outlined text-indigo-600 text-4xl mb-4">school</span>
                                <p class="text-gray-600">
                                    Live and recorded sessions for all user roles.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="relative lg:flex lg:items-center lg:justify-between service-card">
                        <div class="lg:w-5/12 lg:pr-12 order-3 mb-8 lg:mb-0">
                            <div class="lg:hidden absolute -left-8 top-0 w-4 h-4 bg-indigo-600 rounded-full z-10"></div>
                            <h3 class="text-2xl font-semibold text-gray-900 mb-2">Go Live & Support</h3>
                            <p class="text-gray-600">
                                Launch confidently with ongoing expert support.
                            </p>
                        </div>
                        <div class="hidden lg:block w-2/12 text-center order-2">
                            <div
                                class="mx-auto w-16 h-16 bg-indigo-600 rounded-full flex items-center justify-center text-white text-xl font-bold">
                                4</div>
                        </div>
                        <div class="lg:w-5/12 lg:pl-12 order-1">
                            <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
                                <span
                                    class="material-symbols-outlined text-indigo-600 text-4xl mb-4">rocket_launch</span>
                                <p class="text-gray-600">
                                    24/7 support during and after launch.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Integration Section -->
    <section class="py-24 bg-white">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span
                    class="inline-block px-4 py-1.5 text-sm font-semibold text-indigo-600 bg-indigo-100 rounded-full mb-4">Integrations</span>
                <h2 class="text-5xl font-bold text-gray-900 mb-4">Connect Your Tools</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                    Seamlessly integrate HRMS with your existing software stack.
                </p>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-6">
                <div
                    class="bg-white p-6 rounded-xl shadow-lg border border-gray-100 flex items-center justify-center hover:shadow-xl transition-all duration-300">
                    <img src="https://images.unsplash.com/photo-1611162617474-5b21e879e113?ixlib=rb-4.0.3&auto=format&fit=crop&w=150&q=80"
                        alt="Slack" class="h-10 hover:scale-110 transition">
                </div>
                <div
                    class="bg-white p-6 rounded-xl shadow-lg border border-gray-100 flex items-center justify-center hover:shadow-xl transition-all duration-300">
                    <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?ixlib=rb-4.0.3&auto=format&fit=crop&w=150&q=80"
                        alt="Microsoft Teams" class="h-10 hover:scale-110 transition">
                </div>
                <div
                    class="bg-white p-6 rounded-xl shadow-lg border border-gray-100 flex items-center justify-center hover:shadow-xl transition-all duration-300">
                    <img src="{{ asset('itsn.png') }}" alt="Zoom" class="h-10 hover:scale-110 transition">
                </div>
                <div
                    class="bg-white p-6 rounded-xl shadow-lg border border-gray-100 flex items-center justify-center hover:shadow-xl transition-all duration-300">
                    <img src="https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?ixlib=rb-4.0.3&auto=format&fit=crop&w=150&q=80"
                        alt="QuickBooks" class="h-10 hover:scale-110 transition">
                </div>
                <div
                    class="bg-white p-6 rounded-xl shadow-lg border border-gray-100 flex items-center justify-center hover:shadow-xl transition-all duration-300">
                    <img src="https://images.unsplash.com/photo-1593642632823-8f785ba67e45?ixlib=rb-4.0.3&auto=format&fit=crop&w=150&q=80"
                        alt="Xero" class="h-10 hover:scale-110 transition">
                </div>
                <div
                    class="bg-white p-6 rounded-xl shadow-lg border border-gray-100 flex items-center justify-center hover:shadow-xl transition-all duration-300">
                    <img src="https://images.unsplash.com/photo-1611162617474-5b21e879e113?ixlib=rb-4.0.3&auto=format&fit=crop&w=150&q=80"
                        alt="ADP" class="h-10 hover:scale-110 transition">
                </div>
            </div>
            <div class="text-center mt-12">
                <a href="#contact"
                    class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold transition-all duration-300 transform hover:scale-105"
                    aria-label="Request custom integration">
                    Custom Integration?
                    <span class="ml-2 material-symbols-outlined">arrow_forward</span>
                </a>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section id="testimonials" class="py-24 bg-gray-50">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span
                    class="inline-block px-4 py-1.5 text-sm font-semibold text-indigo-600 bg-indigo-100 rounded-full mb-4">Testimonials</span>
                <h2 class="text-5xl font-bold text-gray-900 mb-4">Loved by HR Leaders</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                    Hear from organizations transforming HR with HRMS.
                </p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div
                    class="bg-white p-8 rounded-xl shadow-lg border border-gray-100 service-card transition-all duration-300">
                    <div class="flex items-center mb-4 text-yellow-400">
                        <span class="material-symbols-outlined">star</span>
                        <span class="material-symbols-outlined">star</span>
                        <span class="material-symbols-outlined">star</span>
                        <span class="material-symbols-outlined">star</span>
                        <span class="material-symbols-outlined">star</span>
                    </div>
                    <p class="text-gray-600 italic mb-6">
                        "HRMS streamlined our global HR operations, saving us 40 hours weekly."
                    </p>
                    <div class="flex items-center gap-4">
                        <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?ixlib=rb-4.0.3&auto=format&fit=crop&w=150&q=80"
                            alt="Sarah Chen" class="w-12 h-12 rounded-full">
                        <div>
                            <p class="text-gray-900 font-semibold">Sarah Chen</p>
                            <p class="text-gray-500 text-sm">VP of HR, TechGlobal Inc.</p>
                        </div>
                    </div>
                </div>
                <div
                    class="bg-white p-8 rounded-xl shadow-lg border border-gray-100 service-card transition-all duration-300">
                    <div class="flex items-center mb-4 text-yellow-400">
                        <span class="material-symbols-outlined">star</span>
                        <span class="material-symbols-outlined">star</span>
                        <span class="material-symbols-outlined">star</span>
                        <span class="material-symbols-outlined">star</span>
                        <span class="material-symbols-outlined">star</span>
                    </div>
                    <p class="text-gray-600 italic mb-6">
                        "The self-service portal reduced HR inquiries by 35%. Amazing support!"
                    </p>
                    <div class="flex items-center gap-4">
                        <img src="https://images.unsplash.com/photo-1519085360753-afbcb0a1e3d7?ixlib=rb-4.0.3&auto=format&fit=crop&w=150&q=80"
                            alt="Michael Rodriguez" class="w-12 h-12 rounded-full">
                        <div>
                            <p class="text-gray-900 font-semibold">Michael Rodriguez</p>
                            <p class="text-gray-500 text-sm">HR Director, FinServe Corp</p>
                        </div>
                    </div>
                </div>
                <div
                    class="bg-white p-8 rounded-xl shadow-lg border border-gray-100 service-card transition-all duration-300">
                    <div class="flex items-center mb-4 text-yellow-400">
                        <span class="material-symbols-outlined">star</span>
                        <span class="material-symbols-outlined">star</span>
                        <span class="material-symbols-outlined">star</span>
                        <span class="material-symbols-outlined">star</span>
                        <span class="material-symbols-outlined">star_half</span>
                    </div>
                    <p class="text-gray-600 italic mb-6">
                        "Perfect for startups, with scalability for future growth."
                    </p>
                    <div class="flex items-center gap-4">
                        <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?ixlib=rb-4.0.3&auto=format&fit=crop&w=150&q=80"
                            alt="Priya Patel" class="w-12 h-12 rounded-full">
                        <div>
                            <p class="text-gray-900 font-semibold">Priya Patel</p>
                            <p class="text-gray-500 text-sm">COO, ScaleUp Ventures</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center mt-12">
                <a href="#contact"
                    class="inline-flex items-center text-indigo-600 font-semibold hover:text-indigo-800 transition"
                    aria-label="Read more testimonials">
                    More Success Stories
                    <span class="ml-2 material-symbols-outlined">chevron_right</span>
                </a>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="py-24 bg-white">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span
                    class="inline-block px-4 py-1.5 text-sm font-semibold text-indigo-600 bg-indigo-100 rounded-full mb-4">Pricing</span>
                <h2 class="text-5xl font-bold text-gray-900 mb-4">Flexible Plans for All</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                    Choose a plan tailored to your business. No hidden fees.
                </p>
            </div>
            <div class="flex justify-center mb-10">
                <div class="inline-flex rounded-xl shadow-sm border border-gray-200">
                    <button type="button"
                        class="px-6 py-3 text-sm font-semibold rounded-l-xl bg-white text-gray-700 hover:bg-gray-50 focus:ring-2 focus:ring-indigo-500">Monthly</button>
                    <button type="button"
                        class="px-6 py-3 text-sm font-semibold rounded-r-xl bg-indigo-600 text-white hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500">Annual
                        (Save 20%)</button>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-5xl mx-auto">
                <div
                    class="bg-white p-8 rounded-xl shadow-lg border border-gray-100 service-card transition-all duration-300">
                    <h3 class="text-2xl font-semibold text-gray-900 mb-2">Starter</h3>
                    <p class="text-gray-600 mb-6">For small businesses up to 50 employees</p>
                    <div class="mb-6">
                        <span class="text-4xl font-bold text-gray-900">$49</span>
                        <span class="text-gray-500">/month</span>
                    </div>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-center"><span
                                class="material-symbols-outlined text-emerald-500 mr-2">check_circle</span>Up to 50
                            employees</li>
                        <li class="flex items-center"><span
                                class="material-symbols-outlined text-emerald-500 mr-2">check_circle</span>Core HR
                            management</li>
                        <li class="flex items-center"><span
                                class="material-symbols-outlined text-emerald-500 mr-2">check_circle</span>Basic
                            attendance</li>
                        <li class="flex items-center"><span
                                class="material-symbols-outlined text-emerald-500 mr-2">check_circle</span>Standard
                            reports</li>
                        <li class="flex items-center text-gray-400"><span
                                class="material-symbols-outlined mr-2">cancel</span>Advanced analytics</li>
                    </ul>
                    <a href="#contact"
                        class="block w-full px-6 py-3 bg-gray-100 text-gray-800 rounded-lg hover:bg-gray-200 font-semibold text-center transition"
                        aria-label="Get started with Starter plan">
                        Get Started
                    </a>
                </div>
                <div
                    class="bg-white p-8 rounded-xl shadow-lg border-2 border-indigo-600 transform hover:scale-105 transition-all duration-300 relative">
                    <div
                        class="absolute top-0 right-0 bg-indigo-600 text-white text-xs font-bold px-3 py-1 rounded-bl-lg rounded-tr-xl">
                        Most Popular</div>
                    <h3 class="text-2xl font-semibold text-gray-900 mb-2">Professional</h3>
                    <p class="text-gray-600 mb-6">For businesses up to 200 employees</p>
                    <div class="mb-6">
                        <span class="text-4xl font-bold text-gray-900">$99</span>
                        <span class="text-gray-500">/month</span>
                    </div>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-center"><span
                                class="material-symbols-outlined text-emerald-500 mr-2">check_circle</span>Up to 200
                            employees</li>
                        <li class="flex items-center"><span
                                class="material-symbols-outlined text-emerald-500 mr-2">check_circle</span>Advanced HR
                            tools</li>
                        <li class="flex items-center"><span
                                class="material-symbols-outlined text-emerald-500 mr-2">check_circle</span>Biometric
                            attendance</li>
                        <li class="flex items-center"><span
                                class="material-symbols-outlined text-emerald-500 mr-2">check_circle</span>Custom
                            reports</li>
                        <li class="flex items-center"><span
                                class="material-symbols-outlined text-emerald-500 mr-2">check_circle</span>Basic
                            analytics</li>
                    </ul>
                    <a href="#contact"
                        class="block w-full px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold text-center transition"
                        aria-label="Get started with Professional plan">
                        Get Started
                    </a>
                </div>
                <div
                    class="bg-white p-8 rounded-xl shadow-lg border border-gray-100 service-card transition-all duration-300">
                    <h3 class="text-2xl font-semibold text-gray-900 mb-2">Enterprise</h3>
                    <p class="text-gray-600 mb-6">For large organizations with complex needs</p>
                    <div class="mb-6">
                        <span class="text-4xl font-bold text-gray-900">Custom</span>
                    </div>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-center"><span
                                class="material-symbols-outlined text-emerald-500 mr-2">check_circle</span>Unlimited
                            employees</li>
                        <li class="flex items-center"><span
                                class="material-symbols-outlined text-emerald-500 mr-2">check_circle</span>Enterprise HR
                            tools</li>
                        <li class="flex items-center"><span
                                class="material-symbols-outlined text-emerald-500 mr-2">check_circle</span>Advanced
                            biometrics</li>
                        <li class="flex items-center"><span
                                class="material-symbols-outlined text-emerald-500 mr-2">check_circle</span>Advanced
                            analytics</li>
                        <li class="flex items-center"><span
                                class="material-symbols-outlined text-emerald-500 mr-2">check_circle</span>Performance
                            suite</li>
                    </ul>
                    <a href="#contact"
                        class="block w-full px-6 py-3 bg-gray-100 text-gray-800 rounded-lg hover:bg-gray-200 font-semibold text-center transition"
                        aria-label="Contact sales for Enterprise plan">
                        Contact Sales
                    </a>
                </div>
            </div>
            <div class="text-center mt-12">
                <p class="text-gray-600 mb-4">Need help choosing a plan?</p>
                <a href="#contact"
                    class="inline-flex items-center text-indigo-600 font-semibold hover:text-indigo-800 transition"
                    aria-label="Compare all plans">
                    Compare Plans
                    <span class="ml-2 material-symbols-outlined">chevron_right</span>
                </a>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" class="py-24 bg-gray-50">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-4xl">
            <div class="text-center mb-16">
                <span
                    class="inline-block px-4 py-1.5 text-sm font-semibold text-indigo-600 bg-indigo-100 rounded-full mb-4">FAQ</span>
                <h2 class="text-5xl font-bold text-gray-900 mb-4">Your Questions Answered</h2>
                <p class="text-xl text-gray-600 leading-relaxed">
                    Get clarity on how HRMS can work for you.
                </p>
            </div>
            <div class="space-y-4">
                <div class="border border-gray-200 rounded-xl overflow-hidden service-card">
                    <button
                        class="w-full flex justify-between items-center p-6 text-left focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        <h3 class="text-lg font-semibold text-gray-900">How long does implementation take?</h3>
                        <span
                            class="material-symbols-outlined text-indigo-600 transition-transform duration-300">expand_more</span>
                    </button>
                    <div class="px-6 pb-6 pt-0 accordion-content hidden">
                        <p class="text-gray-600">
                            Implementation takes 2-4 weeks for small to medium businesses and 6-8 weeks for enterprises,
                            with full support from our team.
                        </p>
                    </div>
                </div>
                <div class="border border-gray-200 rounded-xl overflow-hidden service-card">
                    <button
                        class="w-full flex justify-between items-center p-6 text-left focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        <h3 class="text-lg font-semibold text-gray-900">Is my data secure with HRMS?</h3>
                        <span
                            class="material-symbols-outlined text-indigo-600 transition-transform duration-300">expand_more</span>
                    </button>
                    <div class="px-6 pb-6 pt-0 accordion-content hidden">
                        <p class="text-gray-600">
                            Yes, we use 256-bit encryption, SOC 2 Type II data centers, and comply with GDPR and CCPA
                            for maximum security.
                        </p>
                    </div>
                </div>
                <div class="border border-gray-200 rounded-xl overflow-hidden service-card">
                    <button
                        class="w-full flex justify-between items-center p-6 text-left focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        <h3 class="text-lg font-semibold text-gray-900">Can HRMS integrate with our systems?</h3>
                        <span
                            class="material-symbols-outlined text-indigo-600 transition-transform duration-300">expand_more</span>
                    </button>
                    <div class="px-6 pb-6 pt-0 accordion-content hidden">
                        <p class="text-gray-600">
                            HRMS offers pre-built integrations and API access for seamless connectivity with your
                            existing tools.
                        </p>
                    </div>
                </div>
                <div class="border border-gray-200 rounded-xl overflow-hidden service-card">
                    <button
                        class="w-full flex justify-between items-center p-6 text-left focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        <h3 class="text-lg font-semibold text-gray-900">What training and support are provided?</h3>
                        <span
                            class="material-symbols-outlined text-indigo-600 transition-transform duration-300">expand_more</span>
                    </button>
                    <div class="px-6 pb-6 pt-0 accordion-content hidden">
                        <p class="text-gray-600">
                            We offer live training, video tutorials, and 24/7 support, with dedicated account managers
                            for enterprise clients.
                        </p>
                    </div>
                </div>
            </div>
            <div class="text-center mt-12">
                <a href="#contact"
                    class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold transition-all duration-300 transform hover:scale-105"
                    aria-label="Contact support">
                    Contact Support
                    <span class="ml-2 material-symbols-outlined">arrow_forward</span>
                </a>
            </div>
        </div>
    </section>

    <!-- Contact Us Section -->
    <section id="contact" class="py-24 bg-white">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <span
                        class="inline-block px-4 py-1.5 text-sm font-semibold text-indigo-600 bg-indigo-100 rounded-full mb-4">Contact
                        Us</span>
                    <h2 class="text-5xl font-bold text-gray-900 mb-6">Let’s Transform HR</h2>
                    <p class="text-xl text-gray-600 mb-8 leading-relaxed">
                        Reach out to discuss your HR needs or schedule a demo.
                    </p>
                    <div class="space-y-6">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 bg-indigo-600 p-3 rounded-lg text-white">
                                <span class="material-symbols-outlined text-3xl">mail</span>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-1">Email Us</h3>
                                <p class="text-gray-600">support@hrmsplatform.com</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 bg-indigo-600 p-3 rounded-lg text-white">
                                <span class="material-symbols-outlined text-3xl">call</span>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-1">Call Us</h3>
                                <p class="text-gray-600">9812345678</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 bg-indigo-600 p-3 rounded-lg text-white">
                                <span class="material-symbols-outlined text-3xl">schedule</span>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-1">Office Hours</h3>
                                <p class="text-gray-600">Monday - Friday: 8am - 5pm EST</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-8 rounded-xl shadow-lg border border-gray-100">
                    <h3 class="text-2xl font-semibold text-gray-900 mb-6">Send a Message</h3>
                    <form class="space-y-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name <span
                                    class="text-red-600">*</span></label>
                            <input type="text" id="name"
                                class="w-full rounded-lg border border-gray-300 px-4 py-3 text-gray-900 focus:ring-2 focus:ring-indigo-300 focus:border-indigo-500 transition"
                                placeholder="Your Name" required>
                            <p class="text-red-600 text-sm mt-1 hidden" id="name-error">Please enter your name.</p>
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email <span
                                    class="text-red-600">*</span></label>
                            <input type="email" id="email"
                                class="w-full rounded-lg border border-gray-300 px-4 py-3 text-gray-900 focus:ring-2 focus:ring-indigo-300 focus:border-indigo-500 transition"
                                placeholder="your.email@example.com" required>
                            <p class="text-red-600 text-sm mt-1 hidden" id="email-error">Please enter a valid email.</p>
                        </div>
                        <div>
                            <label for="company" class="block text-sm font-medium text-gray-700 mb-1">Company</label>
                            <input type="text" id="company"
                                class="w-full rounded-lg border border-gray-300 px-4 py-3 text-gray-900 focus:ring-2 focus:ring-indigo-300 focus:border-indigo-500 transition"
                                placeholder="Your Company">
                        </div>
                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Message <span
                                    class="text-red-600">*</span></label>
                            <textarea id="message"
                                class="w-full rounded-lg border border-gray-300 px-4 py-3 text-gray-900 focus:ring-2 focus:ring-indigo-300 focus:border-indigo-500 transition"
                                rows="4" placeholder="Tell us about your HR needs..." required></textarea>
                            <p class="text-red-600 text-sm mt-1 hidden" id="message-error">Please enter your message.
                            </p>
                        </div>
                        <div>
                            <button type="submit"
                                class="w-full px-6 py-4 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold text-lg transition transform hover:scale-105"
                                aria-label="Send message">
                                Send Message
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Banner -->
    <section class="py-20 bg-gradient-to-r from-indigo-600 to-blue-700 text-white text-center">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-4xl font-bold mb-4">Ready to Elevate Your HR?</h2>
            <p class="text-xl mb-8 max-w-3xl mx-auto leading-relaxed">
                Join thousands of businesses transforming HR with HRMS.
            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-6">
                <a href="#contact"
                    class="px-10 py-4 bg-emerald-500 text-white rounded-xl hover:bg-emerald-600 font-semibold text-lg transition transform hover:scale-105 shadow-xl"
                    aria-label="Schedule a demo with HRMS">
                    Schedule a Demo
                </a>
                <a href="#pricing"
                    class="px-10 py-4 bg-indigo-800 text-white rounded-xl hover:bg-indigo-900 font-semibold text-lg transition transform hover:scale-105 shadow-xl"
                    aria-label="View pricing plans">
                    View Pricing
                </a>
            </div>
        </div>
    </section>

    <!-- Include Footer -->
    @include('livewire.main.footer')

    <!-- Back to Top Button -->
    <button id="back-to-top"
        class="fixed bottom-8 right-8 w-14 h-14 bg-indigo-600 text-white rounded-full shadow-lg hover:bg-indigo-700 transition-all duration-300 transform hover:scale-110 hidden"
        aria-label="Back to top">
        <span class="material-symbols-outlined text-2xl">arrow_upward</span>
        <div class="absolute inset-0 rounded-full progress-ring" style="--progress: 0%;"></div>
    </button>

    <!-- Client-Side JavaScript -->
    <script>
        // Real-Time Form Validation
        const formInputs = ['name', 'email', 'message'];
        formInputs.forEach(id => {
            const input = document.getElementById(id);
            input.addEventListener('input', () => {
                const error = document.getElementById(`${id}-error`);
                if (id === 'email') {
                    const isValid = input.value && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(input.value);
                    input.classList.toggle('border-red-500', !isValid);
                    input.classList.toggle('animate-shake', !isValid);
                    error.classList.toggle('hidden', isValid);
                } else {
                    const isValid = input.value.trim() !== '';
                    input.classList.toggle('border-red-500', !isValid);
                    input.classList.toggle('animate-shake', !isValid);
                    error.classList.toggle('hidden', isValid);
                }
            });
        });

        document.querySelector('form').addEventListener('submit', function (e) {
            e.preventDefault();
            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const message = document.getElementById('message').value;
            let valid = true;

            if (!name) {
                document.getElementById('name').classList.add('border-red-500', 'animate-shake');
                document.getElementById('name-error').classList.remove('hidden');
                valid = false;
            }
            if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                document.getElementById('email').classList.add('border-red-500', 'animate-shake');
                document.getElementById('email-error').classList.remove('hidden');
                valid = false;
            }
            if (!message) {
                document.getElementById('message').classList.add('border-red-500', 'animate-shake');
                document.getElementById('message-error').classList.remove('hidden');
                valid = false;
            }

            if (valid) {
                alert('Message sent successfully! Our team will respond soon.');
                this.reset();
                formInputs.forEach(id => {
                    document.getElementById(id).classList.remove('border-red-500', 'animate-shake');
                    document.getElementById(`${id}-error`).classList.add('hidden');
                });
            }
        });

        // Back to Top Button with Progress Ring
        const backToTop = document.getElementById('back-to-top');
        window.addEventListener('scroll', () => {
            const scrollTop = window.scrollY;
            const docHeight = document.documentElement.scrollHeight - window.innerHeight;
            const progress = (scrollTop / docHeight) * 100;
            backToTop.style.setProperty('--progress', `${progress}%`);
            backToTop.classList.toggle('hidden', scrollTop <= 300);
        });
        backToTop.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        // FAQ Accordion
        document.querySelectorAll('#faq button').forEach(button => {
            button.addEventListener('click', () => {
                const content = button.nextElementSibling;
                const icon = button.querySelector('span');
                content.classList.toggle('hidden');
                icon.style.transform = content.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
            });
        });

        // Scroll Animations
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fade-in-up');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.2 });

        document.querySelectorAll('.service-card, .feature-card').forEach(card => observer.observe(card));
    </script>
</div>