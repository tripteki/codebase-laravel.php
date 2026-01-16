import { ReactElement, } from "react";
import { Loader2Icon, } from "lucide-react";

import { cn, } from "@/lib/utils";

const Spinner = ({ className, ... props }: React.ComponentProps<"svg">): React.ReactElement =>
{
    return (
        <Loader2Icon
            role="status"
            className={cn ("size-4 animate-spin", className)}
            {... props}
        />
    );
};

export { Spinner, };
