import { FC, type HTMLAttributes, } from "react";

import { cn, } from "@/lib/utils";

interface InputErrorProps extends HTMLAttributes<HTMLParagraphElement>
{
    message?: string;
};

const InputError: FC<InputErrorProps> = ({
    message,
    className = "",
    ...props
}) =>
{
    return message ? (
        <p
            {...props}
            className={cn ("text-sm text-red-600 dark:text-red-400", className)}
        >
            {message}
        </p>
    ) : null;
};

export default InputError;
