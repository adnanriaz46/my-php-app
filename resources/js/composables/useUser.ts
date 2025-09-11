import {usePage} from "@inertiajs/vue3";
import {SharedData, User} from "@/types";
import {UserType, UserTypes} from "@/types/userTypes";

const fullAccessCountiesLimit = 10;
// glEmailTemplates
export function getUser(): User | undefined {
    const page = usePage<SharedData>();
    return page.props?.auth?.user;
}

export function isPremiumUser(userType: UserType | undefined): boolean {
    if (userType) {
        return [UserTypes.PREMIUM, UserTypes.ADMIN].includes(userType);
    }
    return false;
}

export function isFullAccessUser(): boolean {
    const user = getUser();
    if (user && isPremiumUser(user.user_type)) {
        return user.subscribed_counties.length >= fullAccessCountiesLimit;
    }
    return false;
}

export function isCountySubscribedUser(county: string): boolean {
    const user = getUser();
    if (user && isPremiumUser(user.user_type)) {
        return user.subscribed_counties.includes(county);
    }
    return false;
}
