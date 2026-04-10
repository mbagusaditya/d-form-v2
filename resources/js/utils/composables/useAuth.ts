import { computed, ComputedRef } from 'vue';

export default function useAuth(props: Props): ComputedRef<User | null> {
    let user = computed<User | null>(() => props.auth.user);

    return user;
}
