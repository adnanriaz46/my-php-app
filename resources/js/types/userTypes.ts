export const UserTypes = Object.freeze({
    ADMIN: 1,
    FREE: 2,
    PREMIUM: 3,
});

export type UserType = typeof UserTypes[keyof typeof UserTypes];
