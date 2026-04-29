import Link from "next/link";

export type FilterOption = {
  value: string;
  label: string;
};

export function FilterChips({
  basePath,
  paramKey,
  active,
  options,
  allLabel = "All",
}: {
  basePath: string;
  paramKey: string;
  active?: string;
  options: FilterOption[];
  allLabel?: string;
}) {
  const allHref = basePath;
  const isAll = !active;

  return (
    <div className="flex flex-wrap gap-2">
      <Link
        href={allHref}
        className={`inline-flex items-center rounded-full border px-3 py-1 text-xs transition-colors ${
          isAll
            ? "border-primary bg-primary text-white"
            : "border-border hover:border-text"
        }`}
      >
        {allLabel}
      </Link>
      {options.map((option) => {
        const isActive = active === option.value;
        const href = `${basePath}?${paramKey}=${encodeURIComponent(option.value)}`;
        return (
          <Link
            key={option.value}
            href={href}
            className={`inline-flex items-center rounded-full border px-3 py-1 text-xs transition-colors ${
              isActive
                ? "border-primary bg-primary text-white"
                : "border-border hover:border-text"
            }`}
          >
            {option.label}
          </Link>
        );
      })}
    </div>
  );
}
