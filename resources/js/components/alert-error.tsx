import { FC, } from "react";
import { useTranslation, } from "react-i18next";
import { AlertCircleIcon, } from "lucide-react";

import { Alert, AlertDescription, AlertTitle, } from "@/components/ui/alert";

interface AlertErrorProps
{
    errors: string[];
    title?: string;
};

const AlertError: FC<AlertErrorProps> = ({
    errors,
    title,
}) =>
{
    const { t, }: { t: Function; } = useTranslation ();

    return (
        <Alert variant="destructive">
            <AlertCircleIcon />
            <AlertTitle>{title || t ("common.something_went_wrong")}</AlertTitle>
            <AlertDescription>
                <ul className="list-inside list-disc text-sm">
                    {Array.from (new Set (errors)).map ((error, index) => (
                        <li key={index}>{error}</li>
                    ))}
                </ul>
            </AlertDescription>
        </Alert>
    );
};

export default AlertError;
