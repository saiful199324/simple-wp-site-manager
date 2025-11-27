export default function SiteStatusBadge({ status }) {
    const styles = {
        running: "bg-green-100 text-green-800 border-green-300",
        stopped: "bg-yellow-100 text-yellow-800 border-yellow-300",
        failed: "bg-red-100 text-red-800 border-red-300",
        deploying: "bg-blue-100 text-blue-800 border-blue-300",
        unknown: "bg-gray-100 text-gray-800 border-gray-300",
    };

    const label = status?.toUpperCase() || "UNKNOWN";

    return (
        <span
            className={
                `px-2 py-1 text-xs font-semibold rounded border ` +
                (styles[status] || styles.unknown)
            }
        >
            {label}
        </span>
    );
}
