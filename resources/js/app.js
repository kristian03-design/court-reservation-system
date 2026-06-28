import './bootstrap';
import {
    AlertTriangle,
    ArrowLeft,
    ArrowRight,
    BarChart3,
    CalendarCheck,
    CalendarDays,
    CalendarPlus,
    CheckCircle2,
    CircleUserRound,
    CircleHelp,
    ClipboardList,
    Clock,
    CreditCard,
    ChevronDown,
    ChevronLeft,
    ChevronRight,
    Eye,
    EyeOff,
    Grid3x3,
    Home,
    Info,
    KeyRound,
    LayoutDashboard,
    LockKeyhole,
    LogIn,
    LogOut,
    Mail,
    Menu,
    PanelLeftClose,
    PanelLeftOpen,
    Phone,
    PhoneCall,
    Plus,
    QrCode,
    Quote,
    RefreshCw,
    Search,
    Send,
    Settings,
    ShieldCheck,
    Trophy,
    UploadCloud,
    User,
    UserPlus,
    UserRound,
    Users,
    X,
    Zap,
    createIcons,
} from 'lucide';

const renderLucideIcons = () => createIcons({
    icons: {
        AlertTriangle,
        ArrowLeft,
        ArrowRight,
        BarChart3,
        CalendarCheck,
        CalendarDays,
        CalendarPlus,
        CheckCircle2,
        CircleUserRound,
        CircleHelp,
        ClipboardList,
        Clock,
        CreditCard,
        ChevronDown,
        ChevronLeft,
        ChevronRight,
        Eye,
        EyeOff,
        Grid3x3,
        Home,
        Info,
        KeyRound,
        LayoutDashboard,
        LockKeyhole,
        LogIn,
        LogOut,
        Mail,
        Menu,
        PanelLeftClose,
        PanelLeftOpen,
        Phone,
        PhoneCall,
        Plus,
        QrCode,
        Quote,
        RefreshCw,
        Search,
        Send,
        Settings,
        ShieldCheck,
        Trophy,
        UploadCloud,
        User,
        UserPlus,
        UserRound,
        Users,
        X,
        Zap,
    },
    attrs: {
        'stroke-width': 2,
    },
});

window.renderLucideIcons = renderLucideIcons;
window.lucide = { createIcons: renderLucideIcons };

document.addEventListener('DOMContentLoaded', () => {
    renderLucideIcons();

    const togglePasswordBtns = document.querySelectorAll('.toggle-password');
    togglePasswordBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const passwordInput = document.getElementById(targetId);
            const icon = this.querySelector('i');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.setAttribute('data-lucide', 'eye-off');
            } else {
                passwordInput.type = 'password';
                icon.setAttribute('data-lucide', 'eye');
            }
            // Re-render icons for the newly updated data-lucide attribute
            renderLucideIcons();
        });
    });

    // Password confirmation validation
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('password_confirmation');
    
    if (passwordInput && confirmPasswordInput) {
        const validatePassword = () => {
            if (passwordInput.value !== confirmPasswordInput.value) {
                confirmPasswordInput.setCustomValidity("Passwords don't match");
            } else {
                confirmPasswordInput.setCustomValidity('');
            }
        };

        passwordInput.addEventListener('change', validatePassword);
        confirmPasswordInput.addEventListener('keyup', validatePassword);
    }

    const calendarElement = document.getElementById('reservation-calendar');

    if (calendarElement && window.FullCalendar) {
        const calendar = new window.FullCalendar.Calendar(calendarElement, {
            initialView: 'dayGridMonth',
            height: 520,
            events: calendarElement.dataset.eventsUrl,
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek',
            },
        });

        calendar.render();
    }

    // Global Scroll Observer for Scroll Reveal animations
    const observeScrollReveals = () => {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target); // Play animation once
                }
            });
        }, {
            threshold: 0.05,
            rootMargin: '0px 0px -40px 0px'
        });

        document.querySelectorAll('.scroll-reveal').forEach(el => {
            observer.observe(el);
        });
    };

    observeScrollReveals();
});

// Fullscreen Loader Overlay Dismiss Behavior
const dismissLoader = () => {
    const loader = document.getElementById('site-loader');
    if (loader) {
        loader.classList.add('is-loaded');
    }
};

window.addEventListener('load', () => {
    // Add a slight delay so it doesn't flash instantly on fast connections
    setTimeout(dismissLoader, 600);
});
// Fallback timeout to guarantee page interaction after 2.5 seconds
setTimeout(dismissLoader, 2500);

