declare module '@play-ai/agent-web-sdk' {
    export interface PlayAIEvent {
        name: string;
        data?: any;
    }

    export interface PlayAIEvents {
        name: string;
        when: string;
        data: Record<string, { type: string; description: string }>;
    }

    export interface PlayAIOptions {
        events?: readonly PlayAIEvents[];
        onEvent?: (event: PlayAIEvent) => void;
    }

    export function open(webEmbedId: string, options?: PlayAIOptions): Promise<void>;
} 