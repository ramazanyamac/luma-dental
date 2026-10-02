import intlTelInput from "intl-tel-input";
import { en, tr } from "intl-tel-input/locale";
import "intl-tel-input/styles";

const instances = new WeakMap();

export function initPhoneInputs(root = document) {
    const locale = document.documentElement.lang === "tr" ? "tr" : "en";

    root.querySelectorAll("[data-phone-input]").forEach((input) => {
        if (instances.has(input) || input.closest("[hidden]")) {
            return;
        }

        const instance = intlTelInput(input, {
            initialCountry: input.dataset.initialCountry || "tr",
            separateDialCode: true,
            countrySearch: true,
            formatAsYouType: true,
            strictMode: false,
            countryNameLocale: locale,
            uiTranslations: locale === "tr" ? tr : en,

            classNames: {
                selectedCountry:
                    "gap-2! rounded-l-lg! border-r! border-stone-200! bg-stone-50! px-3! text-sm! text-slate-700!",
            },
            placeholderNumberPolicy: "AGGRESSIVE",

            loadUtils: () => import("intl-tel-input/utils"),
        });

        // Yükleme hatası, yakalanmamış Promise hatasına dönüşmesin.
        instance.promise.catch(() => {
            console.error("Telefon biçimlendirme modülü yüklenemedi.");
        });

        instances.set(input, instance);

        input.form?.addEventListener("reset", () => {
            // Tarayıcının alanları sıfırlamasından sonra component'i eşitle.
            setTimeout(() => {
                instance.setNumber(input.value);
                instance.setSelectedCountry(
                    input.dataset.initialCountry || "tr",
                );
            }, 0);
        });
    });
}

export function createPhoneFormData(form) {
    const formData = new FormData(form);

    form.querySelectorAll("[data-phone-input]").forEach((input) => {
        const instance = instances.get(input);
        const country = instance?.getSelectedCountry();

        formData.set(
            `${input.name}_country`,
            country?.iso2?.toUpperCase() || "",
        );
    });

    return formData;
}

initPhoneInputs();
