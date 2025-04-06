const timestamp = Math.floor(Date.now() / 1000);
const email = `jane.doe.${timestamp}@example.org`;

export function fillFakePersonForm() {
    const form = document.getElementById('person-form');
    if (!form) return;

    const fake = {
        firstname: "Jane",
        middlename: "Q",
        lastname: "Doe",
        alias: 'JD',
        email,
        phone: "201 555-1234",
        address: "123 Elm Street",
        secondary_address: "Apt 5B",
        city: "Faketown",
        state: "NY",
        postal_code: "10001",
        notes: "This is fake data for testing.",
        fee: "150.00", // if you're transforming to cents, this will get handled by the transformer
        type: "patient",  // 'patient' or 'payer'
        active: "1" // boolean radio, likely 1 or 0
    };

    for (const [key, value] of Object.entries(fake)) {
        const field = form.querySelector(`[name="person[${key}]"]`);

        if (!field) continue;

        if (field.type === "radio" || field.type === "checkbox") {
            const option = form.querySelector(`[name="person[${key}]"][value="${value}"]`);
            if (option) option.checked = true;
        } else {
            field.value = value;
        }
    }

    // If payer is a <select>, choose the first non-empty option
    const payerSelect = form.querySelector('[name="person[payer]"]');
    if (payerSelect && payerSelect.options.length > 1) {
        payerSelect.selectedIndex = 1;
    }
}
