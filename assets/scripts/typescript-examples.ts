/**
 * TypeScript Examples & Patterns for Hütte9 Project
 *
 * This file demonstrates common TypeScript patterns and best practices
 * for use in the Hütte9 application.
 */

// ============================================
// 1. Type Definitions
// ============================================

// String literal types for theme
type Theme = 'light' | 'dark' | 'system';

// Interface for booking data
interface BookingData {
  checkIn: string;
  checkOut: string;
  guests: number;
  message?: string; // Optional property
}

// Type alias for form validation
type ValidationResult = {
  isValid: boolean;
  errors: string[];
};

// Enum for booking status
enum BookingStatus {
  Pending = 'pending',
  Confirmed = 'confirmed',
  Cancelled = 'cancelled',
}

// ============================================
// 2. DOM Manipulation with Types
// ============================================

/**
 * Example: Type-safe DOM queries
 */
function initializeForm(): void {
  // Query with specific element type
  const form = document.querySelector<HTMLFormElement>('#booking-form');
  const submitButton = document.querySelector<HTMLButtonElement>('#submit-btn');
  const nameInput = document.querySelector<HTMLInputElement>('#name');

  // Type guard - check if element exists
  if (!form || !submitButton || !nameInput) {
    console.error('Required form elements not found');
    return;
  }

  // Now TypeScript knows the types are correct
  form.addEventListener('submit', (event: SubmitEvent) => {
    event.preventDefault();

    // FormData is typed
    const formData = new FormData(form);
    const name = formData.get('name') as string;

    console.log('Submitting form for:', name);
  });

  // Property access is type-safe
  submitButton.disabled = false;
  nameInput.value = '';
}

// ============================================
// 3. Event Handlers
// ============================================

/**
 * Example: Typed event handlers
 */
class BookingForm {
  private form: HTMLFormElement;

  constructor(formSelector: string) {
    const form = document.querySelector<HTMLFormElement>(formSelector);

    if (!form) {
      throw new Error(`Form not found: ${formSelector}`);
    }

    this.form = form;
    this.attachEventListeners();
  }

  private attachEventListeners(): void {
    // Click event
    this.form.addEventListener('click', (event: MouseEvent) => {
      const target = event.target as HTMLElement;

      if (target.matches('.btn-add-guest')) {
        this.handleAddGuest(event);
      }
    });

    // Input event
    this.form.addEventListener('input', (event: Event) => {
      const target = event.target as HTMLInputElement;

      if (target.name === 'guests') {
        this.handleGuestsChange(target.value);
      }
    });

    // Submit event
    this.form.addEventListener('submit', (event: SubmitEvent) => {
      this.handleSubmit(event);
    });
  }

  private handleAddGuest(event: MouseEvent): void {
    event.preventDefault();
    console.log('Adding guest');
  }

  private handleGuestsChange(value: string): void {
    const guests = parseInt(value, 10);
    console.log('Guests changed:', guests);
  }

  private handleSubmit(event: SubmitEvent): void {
    event.preventDefault();
    console.log('Form submitted');
  }
}

// ============================================
// 4. Async/Await with Types
// ============================================

/**
 * Example: Async API calls with proper typing
 */
interface ApiResponse<T> {
  success: boolean;
  data?: T;
  error?: string;
}

interface BookingResponse {
  id: string;
  status: BookingStatus;
  confirmationCode: string;
}

async function submitBooking(data: BookingData): Promise<ApiResponse<BookingResponse>> {
  try {
    const response = await fetch('/api/bookings', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(data),
    });

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const result: BookingResponse = await response.json();

    return {
      success: true,
      data: result,
    };
  } catch (error) {
    return {
      success: false,
      error: error instanceof Error ? error.message : 'Unknown error',
    };
  }
}

// Usage
async function handleBookingSubmit(): Promise<void> {
  const bookingData: BookingData = {
    checkIn: '2025-12-01',
    checkOut: '2025-12-07',
    guests: 2,
    message: 'Looking forward to our stay!',
  };

  const result = await submitBooking(bookingData);

  if (result.success && result.data) {
    console.log('Booking confirmed:', result.data.confirmationCode);
  } else {
    console.error('Booking failed:', result.error);
  }
}

// ============================================
// 5. Custom Type Guards
// ============================================

/**
 * Example: Type guards for runtime checks
 */
function isHTMLInputElement(element: HTMLElement): element is HTMLInputElement {
  return element.tagName === 'INPUT';
}

function isHTMLFormElement(element: Element | null): element is HTMLFormElement {
  return element !== null && element.tagName === 'FORM';
}

function handleElement(element: HTMLElement): void {
  if (isHTMLInputElement(element)) {
    // TypeScript knows element is HTMLInputElement here
    console.log('Input value:', element.value);
    element.disabled = true;
  }
}

// ============================================
// 6. Generic Functions
// ============================================

/**
 * Example: Generic utility functions
 */
function querySelector<T extends HTMLElement>(selector: string): T | null {
  return document.querySelector<T>(selector);
}

function querySelectorAll<T extends HTMLElement>(selector: string): NodeListOf<T> {
  return document.querySelectorAll<T>(selector);
}

// Usage
const button = querySelector<HTMLButtonElement>('.btn-submit');
const inputs = querySelectorAll<HTMLInputElement>('input[type="text"]');

// ============================================
// 7. Class-Based Components
// ============================================

/**
 * Example: Type-safe class component
 */
interface DatePickerConfig {
  format?: string;
  minDate?: Date;
  maxDate?: Date;
  onChange?: (date: Date) => void;
}

class DatePicker {
  private element: HTMLInputElement;
  private config: Required<DatePickerConfig>;
  private selectedDate: Date | null = null;

  constructor(selector: string, config: DatePickerConfig = {}) {
    const element = document.querySelector<HTMLInputElement>(selector);

    if (!element) {
      throw new Error(`Element not found: ${selector}`);
    }

    this.element = element;
    this.config = this.mergeConfig(config);
    this.init();
  }

  private mergeConfig(config: DatePickerConfig): Required<DatePickerConfig> {
    return {
      format: config.format ?? 'YYYY-MM-DD',
      minDate: config.minDate ?? new Date(),
      maxDate: config.maxDate ?? new Date(Date.now() + 365 * 24 * 60 * 60 * 1000),
      onChange: config.onChange ?? (() => {}),
    };
  }

  private init(): void {
    this.element.addEventListener('change', this.handleChange.bind(this));
  }

  private handleChange(event: Event): void {
    const target = event.target as HTMLInputElement;
    const date = new Date(target.value);

    if (this.isValidDate(date)) {
      this.selectedDate = date;
      this.config.onChange(date);
    }
  }

  private isValidDate(date: Date): boolean {
    return date >= this.config.minDate && date <= this.config.maxDate;
  }

  public getValue(): Date | null {
    return this.selectedDate;
  }

  public setValue(date: Date): void {
    if (this.isValidDate(date)) {
      this.selectedDate = date;
      this.element.value = this.formatDate(date);
    }
  }

  private formatDate(date: Date): string {
    // Simplified formatting
    return date.toISOString().split('T')[0];
  }
}

// ============================================
// 8. Utility Functions with Types
// ============================================

/**
 * Example: Utility functions with proper types
 */
function debounce<T extends (...args: any[]) => any>(
  func: T,
  delay: number
): (...args: Parameters<T>) => void {
  let timeoutId: ReturnType<typeof setTimeout> | undefined;

  return (...args: Parameters<T>) => {
    if (timeoutId) {
      clearTimeout(timeoutId);
    }

    timeoutId = setTimeout(() => {
      func(...args);
    }, delay);
  };
}

function throttle<T extends (...args: any[]) => any>(
  func: T,
  delay: number
): (...args: Parameters<T>) => void {
  let lastCall = 0;

  return (...args: Parameters<T>) => {
    const now = Date.now();

    if (now - lastCall >= delay) {
      lastCall = now;
      func(...args);
    }
  };
}

// Usage
const handleSearch = debounce((query: string) => {
  console.log('Searching for:', query);
}, 300);

const handleScroll = throttle(() => {
  console.log('Scrolling...');
}, 100);

// ============================================
// 9. Local Storage with Types
// ============================================

/**
 * Example: Type-safe local storage wrapper
 */
class Storage<T> {
  constructor(private key: string) {}

  get(): T | null {
    try {
      const item = localStorage.getItem(this.key);
      return item ? JSON.parse(item) : null;
    } catch (error) {
      console.error('Error reading from storage:', error);
      return null;
    }
  }

  set(value: T): void {
    try {
      localStorage.setItem(this.key, JSON.stringify(value));
    } catch (error) {
      console.error('Error writing to storage:', error);
    }
  }

  remove(): void {
    try {
      localStorage.removeItem(this.key);
    } catch (error) {
      console.error('Error removing from storage:', error);
    }
  }
}

// Usage
interface UserPreferences {
  theme: Theme;
  language: 'en' | 'de';
  notifications: boolean;
}

const preferences = new Storage<UserPreferences>('user-preferences');

// Type-safe operations
preferences.set({
  theme: 'dark',
  language: 'en',
  notifications: true,
});

const savedPrefs = preferences.get();
if (savedPrefs) {
  console.log('Theme:', savedPrefs.theme); // Type-safe access
}

// ============================================
// 10. Error Handling with Types
// ============================================

/**
 * Example: Custom error classes
 */
class ValidationError extends Error {
  constructor(
    message: string,
    public field: string,
    public value: any
  ) {
    super(message);
    this.name = 'ValidationError';
  }
}

class ApiError extends Error {
  constructor(
    message: string,
    public statusCode: number,
    public response?: any
  ) {
    super(message);
    this.name = 'ApiError';
  }
}

function validateBooking(data: BookingData): void {
  if (!data.checkIn) {
    throw new ValidationError('Check-in date is required', 'checkIn', data.checkIn);
  }

  if (data.guests < 1 || data.guests > 10) {
    throw new ValidationError('Guests must be between 1 and 10', 'guests', data.guests);
  }
}

// Usage with proper error handling
try {
  validateBooking({ checkIn: '', checkOut: '2025-12-07', guests: 0 });
} catch (error) {
  if (error instanceof ValidationError) {
    console.error(`Validation error in ${error.field}: ${error.message}`);
  } else if (error instanceof ApiError) {
    console.error(`API error (${error.statusCode}): ${error.message}`);
  } else {
    console.error('Unknown error:', error);
  }
}

// ============================================
// 11. Observer Pattern with Types
// ============================================

/**
 * Example: Type-safe event emitter
 */
type EventCallback<T = any> = (data: T) => void;

class EventEmitter<Events extends Record<string, any>> {
  private listeners = new Map<keyof Events, Set<EventCallback>>();

  on<K extends keyof Events>(event: K, callback: EventCallback<Events[K]>): void {
    if (!this.listeners.has(event)) {
      this.listeners.set(event, new Set());
    }
    this.listeners.get(event)!.add(callback);
  }

  off<K extends keyof Events>(event: K, callback: EventCallback<Events[K]>): void {
    this.listeners.get(event)?.delete(callback);
  }

  emit<K extends keyof Events>(event: K, data: Events[K]): void {
    this.listeners.get(event)?.forEach(callback => callback(data));
  }
}

// Usage
interface BookingEvents {
  'booking:created': { id: string; date: Date };
  'booking:cancelled': { id: string; reason: string };
  'booking:confirmed': { id: string };
}

const bookingEmitter = new EventEmitter<BookingEvents>();

// Type-safe event listeners
bookingEmitter.on('booking:created', (data) => {
  // data is typed as { id: string; date: Date }
  console.log('Booking created:', data.id);
});

bookingEmitter.on('booking:cancelled', (data) => {
  // data is typed as { id: string; reason: string }
  console.log('Booking cancelled:', data.reason);
});

// Type-safe event emission
bookingEmitter.emit('booking:created', {
  id: '123',
  date: new Date(),
});

// ============================================
// Export examples for use in other files
// ============================================

export {
  BookingData,
  ValidationResult,
  BookingStatus,
  initializeForm,
  submitBooking,
  DatePicker,
  debounce,
  throttle,
  Storage,
  EventEmitter,
};
