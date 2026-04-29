import Link from "next/link";
import type { Service } from "@/lib/types";

export function ServiceCard({ service }: { service: Service }) {
  return (
    <Link
      href={`/services/${service.slug}`}
      className="group flex h-full flex-col rounded-2xl border border-border bg-surface p-6 transition-shadow hover:shadow-lg"
    >
      <h3 className="text-lg font-semibold tracking-tight group-hover:text-primary transition-colors">
        {service.title}
      </h3>
      <p className="mt-2 flex-1 text-sm text-muted line-clamp-3">
        {service.summary}
      </p>
      {service.cta_label ? (
        <p className="mt-4 text-xs font-medium uppercase tracking-wider text-muted group-hover:text-primary transition-colors">
          {service.cta_label} →
        </p>
      ) : null}
    </Link>
  );
}
