const nameCheck = /^[-_a-zA-Z0-9]{4,22}$/;
const tokenCheck = /^[-_/+a-zA-Z0-9]{24,}$/;

// Type definitions
interface CsrfHeaders {
  [key: string]: string;
}

interface TurboSubmitStartEvent extends CustomEvent {
  detail: {
    formSubmission: {
      formElement: HTMLFormElement;
      fetchRequest: {
        headers: Record<string, string>;
      };
    };
  };
}

interface TurboSubmitEndEvent extends CustomEvent {
  detail: {
    formSubmission: {
      formElement: HTMLFormElement;
    };
  };
}

// Generate and double-submit a CSRF token in a form field and a cookie, as defined by Symfony's SameOriginCsrfTokenManager
// Use `form.requestSubmit()` to ensure that the submit event is triggered. Using `form.submit()` will not trigger the event
// and thus this event-listener will not be executed.
document.addEventListener('submit', function (event: SubmitEvent) {
  if (event.target instanceof HTMLFormElement) {
    generateCsrfToken(event.target);
  }
}, true);

// When @hotwired/turbo handles form submissions, send the CSRF token in a header in addition to a cookie
// The `framework.csrf_protection.check_header` config option needs to be enabled for the header to be checked
document.addEventListener('turbo:submit-start', function (event: Event) {
  const turboEvent = event as TurboSubmitStartEvent;
  const headers = generateCsrfHeaders(turboEvent.detail.formSubmission.formElement);
  Object.keys(headers).forEach(key => {
    turboEvent.detail.formSubmission.fetchRequest.headers[key] = headers[key];
  });
});

// When @hotwired/turbo handles form submissions, remove the CSRF cookie once a form has been submitted
document.addEventListener('turbo:submit-end', function (event: Event) {
  const turboEvent = event as TurboSubmitEndEvent;
  removeCsrfToken(turboEvent.detail.formSubmission.formElement);
});

export function generateCsrfToken(formElement: HTMLFormElement): void {
  const csrfField = formElement.querySelector<HTMLInputElement>(
    'input[data-controller="csrf-protection"], input[name="_csrf_token"]'
  );

  if (!csrfField) {
    return;
  }

  let csrfCookie = csrfField.getAttribute('data-csrf-protection-cookie-value');
  let csrfToken = csrfField.value;

  if (!csrfCookie && nameCheck.test(csrfToken)) {
    csrfField.setAttribute('data-csrf-protection-cookie-value', csrfCookie = csrfToken);
    const crypto = window.crypto || (window as any).msCrypto;
    csrfField.defaultValue = csrfToken = btoa(
      String.fromCharCode.apply(null, Array.from(crypto.getRandomValues(new Uint8Array(18))))
    );
  }
  csrfField.dispatchEvent(new Event('change', { bubbles: true }));

  if (csrfCookie && tokenCheck.test(csrfToken)) {
    const cookie = `${csrfCookie}_${csrfToken}=${csrfCookie}; path=/; samesite=strict`;
    document.cookie = window.location.protocol === 'https:'
      ? `__Host-${cookie}; secure`
      : cookie;
  }
}

export function generateCsrfHeaders(formElement: HTMLFormElement): CsrfHeaders {
  const headers: CsrfHeaders = {};
  const csrfField = formElement.querySelector<HTMLInputElement>(
    'input[data-controller="csrf-protection"], input[name="_csrf_token"]'
  );

  if (!csrfField) {
    return headers;
  }

  const csrfCookie = csrfField.getAttribute('data-csrf-protection-cookie-value');

  if (tokenCheck.test(csrfField.value) && csrfCookie && nameCheck.test(csrfCookie)) {
    headers[csrfCookie] = csrfField.value;
  }

  return headers;
}

export function removeCsrfToken(formElement: HTMLFormElement): void {
  const csrfField = formElement.querySelector<HTMLInputElement>(
    'input[data-controller="csrf-protection"], input[name="_csrf_token"]'
  );

  if (!csrfField) {
    return;
  }

  const csrfCookie = csrfField.getAttribute('data-csrf-protection-cookie-value');

  if (tokenCheck.test(csrfField.value) && csrfCookie && nameCheck.test(csrfCookie)) {
    const cookie = `${csrfCookie}_${csrfField.value}=0; path=/; samesite=strict; max-age=0`;
    document.cookie = window.location.protocol === 'https:'
      ? `__Host-${cookie}; secure`
      : cookie;
  }
}

/* stimulusFetch: 'lazy' */
export default 'csrf-protection-controller';
