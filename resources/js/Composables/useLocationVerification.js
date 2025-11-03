import { ref, onMounted } from 'vue';

export function useLocationVerification() {
    const locationVerified = ref(false);
    const locationError = ref(null);
    const isChecking = ref(false);
    const userLocation = ref(null);

    const requestLocation = () => {
        return new Promise((resolve, reject) => {
            isChecking.value = true;
            locationError.value = null;

            if (!navigator.geolocation) {
                const error = 'Geolocation is not supported by your browser';
                locationError.value = error;
                isChecking.value = false;
                reject(error);
                return;
            }

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    userLocation.value = {
                        latitude: position.coords.latitude,
                        longitude: position.coords.longitude,
                        accuracy: position.coords.accuracy
                    };
                    locationVerified.value = true;
                    isChecking.value = false;
                    resolve(userLocation.value);
                },
                (error) => {
                    let errorMessage = '';
                    switch (error.code) {
                        case error.PERMISSION_DENIED:
                            errorMessage = 'Location permission denied. Please enable location access to continue.';
                            break;
                        case error.POSITION_UNAVAILABLE:
                            errorMessage = 'Location information is unavailable.';
                            break;
                        case error.TIMEOUT:
                            errorMessage = 'Location request timed out.';
                            break;
                        default:
                            errorMessage = 'An unknown error occurred while getting location.';
                    }
                    locationError.value = errorMessage;
                    locationVerified.value = false;
                    isChecking.value = false;
                    reject(errorMessage);
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        });
    };

    return {
        locationVerified,
        locationError,
        isChecking,
        userLocation,
        requestLocation
    };
}
